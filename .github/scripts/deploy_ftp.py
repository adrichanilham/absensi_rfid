import argparse
import errno
import ftplib
import hashlib
import os
import re
import socket
import ssl
import threading
import time
from contextlib import suppress
from pathlib import Path, PurePosixPath


APP_DIRECTORIES = ("app", "bootstrap", "config", "resources", "routes", "vendor")
DATABASE_DIRECTORIES = ("factories", "migrations", "seeders")
APP_FILES = (".htaccess", "artisan", "composer.json", "composer.lock")
RUNTIME_DIRECTORIES = (
    "bootstrap/cache",
    "storage/app/private",
    "storage/app/public",
    "storage/framework/cache/data",
    "storage/framework/sessions",
    "storage/framework/views",
    "storage/logs",
)
PUBLIC_FILES = (
    ".htaccess", "favicon.ico", "images/sekolah.jpg", "index.php", "manifest.webmanifest",
    "pwa-192x192.png", "pwa-512x512.png", "pwa-maskable-512x512.png", "robots.txt", "sw.js",
)
VITE_MANIFESTS = ("build/fonts-manifest.json", "build/manifest.json")
PUBLIC_PUBLISH_LAST = (*VITE_MANIFESTS, "manifest.webmanifest", "sw.js", "index.php")
REMOTE_PUBLIC = PurePosixPath("/htdocs")
REMOTE_APP = REMOTE_PUBLIC / "laravel_app"
SOCKET_TIMEOUT = 30
MAX_RETRIES = 2
FTP_ERRORS = (ValueError, OSError, EOFError, ftplib.Error)
UNAVAILABLE_VERIFICATION_CODES = {"500", "502", "504", "550"}


def excluded_name(name):
    name = name.lower()
    return (
        name.startswith((".env", ".git", ".ftp-deploy-"))
        or name in {
            "auth.json", "credentials.json", "secrets.json", ".htpasswd", ".netrc", ".npmrc",
            "node_modules", "uploads", "user-uploads", "id_rsa", "id_ed25519",
        }
        or re.search(r"\.(?:sqlite3?|db3?|s3db|sl3)(?:[-.].*)?$|\.sql(?:\..*)?$", name) is not None
        or name.endswith((".key", ".pem", ".p12", ".pfx", ".dump"))
    )


def app_file_allowed(path):
    if len(path.parts) == 1:
        return path.name in APP_FILES
    if path.parts[0] == "database":
        return len(path.parts) >= 3 and path.parts[1] in DATABASE_DIRECTORIES and path.suffix == ".php"
    return path.parts[0] in APP_DIRECTORIES and not (path.parts[0] == "bootstrap" and path.parts[1].lower() == "cache")


def public_file_allowed(path):
    name = path.as_posix()
    return (
        name in (*PUBLIC_FILES, *VITE_MANIFESTS)
        or re.fullmatch(r"workbox-[\w-]+\.js", name) is not None
        or re.fullmatch(r"build/assets/(?:[\w.-]+/)*[\w.-]+-[\w-]{8,}\.(?:css|js|woff2?)", name) is not None
    )


def package_files(root, is_allowed):
    if root.is_symlink() or not root.is_dir():
        raise ValueError(f"Missing or invalid deployment directory: {root}")

    files = []
    for path in sorted(root.rglob("*")):
        relative = PurePosixPath(path.relative_to(root).as_posix())
        if path.is_symlink() or any(excluded_name(part) for part in relative.parts) or any(
            character in relative.as_posix() for character in "\\\r\n"
        ):
            raise ValueError(f"Unsafe deployment path: {path}")
        if path.is_dir():
            continue
        if not path.is_file() or not is_allowed(relative):
            raise ValueError(f"Unexpected deployment file: {path}")
        files.append(relative)
    return files


def deployment_files(package):
    if package.is_symlink():
        raise ValueError(f"Unexpected symbolic link for deployment package: {package}")
    app = package_files(package / "app", app_file_allowed)
    public = package_files(package / "public", public_file_allowed)
    for files, required in (
        (app, (".htaccess", "bootstrap/app.php", "vendor/autoload.php")),
        (public, (".htaccess", "index.php", "sw.js", "manifest.webmanifest", "build/manifest.json")),
    ):
        missing = set(map(PurePosixPath, required)).difference(files)
        if missing:
            raise ValueError(f"Missing deployment files: {', '.join(map(str, sorted(missing)))}")
    return app, public


def ensure_directory(ftp, path, available):
    if path in available:
        return
    ensure_directory(ftp, path.parent, available)
    try:
        ftp.cwd(path.as_posix())
    except ftplib.error_perm as error:
        if not str(error).startswith("550"):
            raise
        ftp.mkd(path.as_posix())
        ftp.cwd(path.as_posix())
    available.add(path)


class FTPSSetupError(ftplib.Error):
    pass


def is_transient(error):
    if isinstance(error, ssl.SSLError):
        return False
    if isinstance(error, ftplib.error_temp):
        return str(error)[:3] in {"421", "425", "426", "450", "451"}
    if isinstance(error, socket.gaierror):
        return error.errno == socket.EAI_AGAIN
    return isinstance(error, (ConnectionError, TimeoutError, EOFError)) or (
        isinstance(error, OSError) and error.errno in {errno.ENETDOWN, errno.ENETUNREACH, errno.EHOSTUNREACH}
    )


class FTPSession:
    def __init__(self, context, server, username, password):
        self.context = context
        self.server = server
        self.username = username
        self.password = password
        self.ftp = None
        self.available = set()

    def __enter__(self):
        return self

    def __exit__(self, exception_type, exception, traceback):
        if exception_type is None and self.ftp is not None:
            with suppress(*FTP_ERRORS):
                self.ftp.quit()
        self.close()

    def close(self):
        if self.ftp is not None:
            with suppress(OSError):
                self.ftp.close()
            self.ftp = None
        self.available.clear()

    def open(self):
        self.ftp = ftplib.FTP_TLS(context=self.context, timeout=SOCKET_TIMEOUT)
        self.ftp.connect(self.server, port=21)
        try:
            self.ftp.auth()
            self.ftp.login(self.username, self.password)
            self.ftp.prot_p()
        except ssl.SSLError:
            raise
        except FTP_ERRORS:
            raise FTPSSetupError() from None
        self.ftp.set_pasv(True)
        self.ftp.voidcmd("TYPE I")  # SIZE and SHA-256 must both describe binary data.
        self.ftp.cwd(REMOTE_PUBLIC.as_posix())
        self.available.add(REMOTE_PUBLIC)

    def run(self, operation):
        for attempt in range(MAX_RETRIES + 1):
            try:
                if self.ftp is None:
                    self.open()
                return operation(self.ftp, self.available)
            except FTP_ERRORS as error:
                self.close()
                if attempt == MAX_RETRIES or not is_transient(error):
                    raise
                print(f"Temporary FTPS failure ({type(error).__name__}); reconnecting, retry {attempt + 1}/{MAX_RETRIES}.",
                      flush=True)
                time.sleep(2 ** attempt)


class Progress:
    def __init__(self, total_files, total_bytes):
        self.total_files = total_files
        self.total_bytes = total_bytes
        self.completed_files = 0
        self.completed_bytes = 0
        self.uploaded_bytes = 0
        self.verified_files = 0
        self.lock = threading.Lock()
        self.stopped = threading.Event()

    def __enter__(self):
        self.started = time.monotonic()
        self.report("started")
        self.worker = threading.Thread(target=self.heartbeat, daemon=True)
        self.worker.start()
        return self

    def __exit__(self, exception_type, exception, traceback):
        self.stopped.set()
        self.worker.join()
        self.report("completed" if exception_type is None else "stopped")

    def heartbeat(self):
        while not self.stopped.wait(30):
            self.report("in progress")

    def report(self, phase):
        with self.lock:
            self._print_progress(phase)

    def _print_progress(self, phase):
        elapsed = time.monotonic() - self.started
        rate = self.uploaded_bytes / elapsed if elapsed > 0 else 0
        print(
            f"FTPS {phase}: {self.completed_files}/{self.total_files} files, "
            f"{self.completed_bytes}/{self.total_bytes} bytes complete, "
            f"{self.verified_files} verified without upload, {self.uploaded_bytes} bytes uploaded, "
            f"{rate:.1f} uploaded bytes/s average, elapsed {elapsed:.1f}s.",
            flush=True,
        )

    def complete_file(self, size, verified):
        with self.lock:
            self.completed_files += 1
            self.completed_bytes += size
            if verified:
                self.verified_files += 1
            else:
                self.uploaded_bytes += size
            if self.completed_files % 100 == 0:
                self._print_progress("in progress")


def remote_matches(ftp, destination, source, size):
    try:
        if ftp.size(destination) != size:
            return False
        remote_hash = hashlib.sha256()
        ftp.retrbinary(f"RETR {destination}", remote_hash.update)
    except ftplib.error_perm as error:
        if str(error)[:3] in UNAVAILABLE_VERIFICATION_CODES:
            return False
        raise
    with source.open("rb") as file:
        return remote_hash.digest() == hashlib.file_digest(file, "sha256").digest()


def upload_file(ftp, available, destination, source, size, resume):
    if resume and remote_matches(ftp, destination.as_posix(), source, size):
        return True
    ensure_directory(ftp, destination.parent, available)
    with source.open("rb") as file:
        ftp.storbinary(f"STOR {destination.as_posix()}", file)
    return False


def upload_packages(session, package, app, public, *, resume=False):
    publish_last = tuple(map(PurePosixPath, PUBLIC_PUBLISH_LAST))
    public = [path for path in public if path not in publish_last] + [
        path for path in publish_last if path in public
    ]
    transfers = []
    for local, remote, files in (("app", REMOTE_APP, app), ("public", REMOTE_PUBLIC, public)):
        for relative in files:
            source = package / local / relative
            transfers.append((remote / relative, source, source.stat().st_size))

    with Progress(len(transfers), sum(size for _, _, size in transfers)) as progress:
        # Provision runtime directories without uploading any of their contents.
        for directory in RUNTIME_DIRECTORIES:
            session.run(lambda ftp, available: ensure_directory(ftp, REMOTE_APP / directory, available))
        for destination, source, size in transfers:
            verified = session.run(lambda ftp, available: upload_file(ftp, available, destination, source, size, resume))
            progress.complete_file(size, verified)


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("package", nargs="?", type=Path, default=Path("deploy"))
    parser.add_argument("--resume", action="store_true", help="Skip files only after verifying their remote content")
    args = parser.parse_args()

    try:
        app, public = deployment_files(args.package)
    except (ValueError, OSError) as error:
        raise SystemExit(f"Invalid deployment package: {error}") from None

    server, username, password = (os.environ.get(name) for name in ("FTP_SERVER", "FTP_USERNAME", "FTP_PASSWORD"))
    if not all((server, username, password)):
        raise SystemExit("FTP_SERVER, FTP_USERNAME and FTP_PASSWORD are required")

    try:
        context = ssl.SSLContext(ssl.PROTOCOL_TLS_CLIENT)
        context.load_default_certs(ssl.Purpose.SERVER_AUTH)
        context.minimum_version = ssl.TLSVersion.TLSv1_2
        with FTPSession(context, server, username, password) as session:
            upload_packages(session, args.package, app, public, resume=args.resume)
    except ssl.SSLCertVerificationError:
        raise SystemExit("Deployment failed: FTPS certificate or hostname verification failed.") from None
    except ssl.SSLError:
        raise SystemExit("Deployment failed: FTPS TLS handshake or transfer failed.") from None
    except FTP_ERRORS:
        # Server replies may echo login details, so keep them out of the workflow log.
        raise SystemExit("Deployment failed: FTPS connection, login or transfer failed.") from None


if __name__ == "__main__":
    main()
