import argparse
import ftplib
import os
import re
import ssl
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


def upload_packages(ftp, package, app, public):
    ftp.cwd(REMOTE_PUBLIC.as_posix())
    available = {REMOTE_PUBLIC}
    # Empty runtime directories can be provisioned without uploading any of their contents.
    for directory in RUNTIME_DIRECTORIES:
        ensure_directory(ftp, REMOTE_APP / directory, available)

    publish_last = tuple(map(PurePosixPath, PUBLIC_PUBLISH_LAST))
    public = [path for path in public if path not in publish_last] + [
        path for path in publish_last if path in public
    ]
    for local, remote, files in (("app", REMOTE_APP, app), ("public", REMOTE_PUBLIC, public)):
        for relative in files:
            destination = remote / relative
            ensure_directory(ftp, destination.parent, available)
            with (package / local / relative).open("rb") as source:
                ftp.storbinary(f"STOR {destination.as_posix()}", source)
        print(f"Uploaded {len(files)} files to {remote}.")


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("package", nargs="?", type=Path, default=Path("deploy"))
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
        with ftplib.FTP_TLS(context=context, timeout=30) as ftp:
            ftp.connect(server, port=21)
            ftp.auth()
            ftp.login(username, password)
            ftp.prot_p()
            ftp.set_pasv(True)
            upload_packages(ftp, args.package, app, public)
    except ssl.SSLCertVerificationError:
        raise SystemExit("Deployment failed: FTPS certificate or hostname verification failed.") from None
    except ssl.SSLError:
        raise SystemExit("Deployment failed: FTPS TLS handshake or transfer failed.") from None
    except (ValueError, OSError, EOFError, ftplib.Error):
        # Server replies may echo login details, so keep them out of the workflow log.
        raise SystemExit("Deployment failed: FTPS connection, login or transfer failed.") from None


if __name__ == "__main__":
    main()
