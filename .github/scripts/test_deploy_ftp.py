import ftplib
import io
import json
import os
import ssl
import struct
import subprocess
import sys
import tempfile
import unittest
from contextlib import redirect_stderr, redirect_stdout
from pathlib import Path, PurePosixPath
from unittest.mock import ANY, Mock, call, patch

from deploy_ftp import FTPSession, Progress, REMOTE_PUBLIC, deployment_files, main, upload_file, upload_packages


PACKAGER = Path(__file__).with_name("package-deploy.py").resolve()


def write_file(root, name, content):
    path = root / name
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_bytes(content if isinstance(content, bytes) else content.encode())


class SimulatedFTP:
    def __init__(self, files):
        self.files = files.copy()
        self.directories = {PurePosixPath("/htdocs")}
        self.directories.update(directory for name in files for directory in PurePosixPath(name).parents)
        self.created = []
        self.uploaded = []
        self.size_checked = []
        self.retrieved = []
        self.available = {REMOTE_PUBLIC}

    def run(self, operation):
        return operation(self, self.available)

    def size(self, name):
        self.size_checked.append(name)
        if name not in self.files:
            raise ftplib.error_perm("550 File unavailable")
        return len(self.files[name])

    def retrbinary(self, command, callback):
        verb, name = command.split(" ", 1)
        if verb != "RETR":
            raise AssertionError(command)
        self.retrieved.append(name)
        for offset in range(0, len(self.files[name]), 3):
            callback(self.files[name][offset:offset + 3])

    def cwd(self, name):
        if PurePosixPath(name) not in self.directories:
            raise ftplib.error_perm("550 Directory unavailable")

    def mkd(self, name):
        path = PurePosixPath(name)
        if path in self.directories or name in self.files or path.parent not in self.directories:
            raise ftplib.error_perm("550 Cannot create directory")
        self.directories.add(path)
        self.created.append(name)

    def storbinary(self, command, source):
        verb, destination = command.split(" ", 1)
        if verb != "STOR" or PurePosixPath(destination).parent not in self.directories:
            raise AssertionError(command)
        self.files[destination] = source.read()
        self.uploaded.append(destination)


class DeployFTPSConnectionTest(unittest.TestCase):
    def setUp(self):
        self.credentials = {
            "FTP_SERVER": "ftp-fixture.invalid",
            "FTP_USERNAME": "fixture-login-user",
            "FTP_PASSWORD": "fixture-login-password",
        }
        self.enterContext(patch.dict(os.environ, self.credentials))
        self.enterContext(patch.object(sys, "argv", ["deploy_ftp.py"]))
        self.enterContext(patch("deploy_ftp.deployment_files", return_value=([], [])))
        self.plain_ftp = self.enterContext(patch("deploy_ftp.ftplib.FTP"))
        self.tls_ftp = self.enterContext(patch("deploy_ftp.ftplib.FTP_TLS"))
        self.client = self.tls_ftp.return_value
        self.upload = self.enterContext(patch("deploy_ftp.upload_packages", side_effect=self.connect_for_upload))

    def connect_for_upload(self, session, *args, **kwargs):
        session.run(lambda ftp, available: None)

    def test_explicit_tls_protects_control_and_passive_data_before_upload(self):
        with redirect_stdout(io.StringIO()) as stdout, redirect_stderr(io.StringIO()) as stderr:
            main()

        self.tls_ftp.assert_called_once_with(context=ANY, timeout=30)
        context = self.tls_ftp.call_args.kwargs["context"]
        self.assertTrue(context.check_hostname)
        self.assertEqual(context.verify_mode, ssl.CERT_REQUIRED)
        self.assertEqual(context.minimum_version, ssl.TLSVersion.TLSv1_2)
        self.assertIsNone(context.keylog_filename)
        self.assertEqual(self.client.mock_calls, [
            call.connect(self.credentials["FTP_SERVER"], port=21),
            call.auth(),
            call.login(self.credentials["FTP_USERNAME"], self.credentials["FTP_PASSWORD"]),
            call.prot_p(),
            call.set_pasv(True),
            call.voidcmd("TYPE I"),
            call.cwd("/htdocs"),
            call.quit(),
            call.close(),
        ])
        self.upload.assert_called_once_with(ANY, Path("deploy"), [], [], resume=False)
        self.plain_ftp.assert_not_called()
        for value in self.credentials.values():
            self.assertNotIn(value, stdout.getvalue() + stderr.getvalue())

    def test_certificate_verification_failure_stops_before_login_without_fallback(self):
        self.client.auth.side_effect = ssl.SSLCertVerificationError("certificate mismatch")
        with self.assertRaisesRegex(SystemExit, "FTPS certificate or hostname verification failed"):
            main()

        self.client.login.assert_not_called()
        self.client.prot_p.assert_not_called()
        self.client.cwd.assert_not_called()
        self.tls_ftp.assert_called_once()
        self.plain_ftp.assert_not_called()

    def test_auth_tls_rejection_stops_before_login_without_fallback(self):
        self.client.auth.side_effect = ftplib.error_perm("502 AUTH TLS unavailable")
        with self.assertRaisesRegex(SystemExit, "FTPS connection, login or transfer failed"):
            main()

        self.client.login.assert_not_called()
        self.client.cwd.assert_not_called()
        self.tls_ftp.assert_called_once()
        self.plain_ftp.assert_not_called()

    def test_data_protection_rejection_prevents_upload_without_fallback(self):
        self.client.prot_p.side_effect = ftplib.error_perm("534 PROT P rejected")
        with self.assertRaisesRegex(SystemExit, "FTPS connection, login or transfer failed"):
            main()

        self.client.cwd.assert_not_called()
        self.tls_ftp.assert_called_once()
        self.plain_ftp.assert_not_called()

    def test_data_tls_handshake_failure_stops_without_fallback(self):
        self.upload.side_effect = ssl.SSLError("data TLS handshake failed")
        with self.assertRaisesRegex(SystemExit, "FTPS TLS handshake or transfer failed"):
            main()

        self.upload.assert_called_once()
        self.plain_ftp.assert_not_called()

    def test_login_error_does_not_expose_credentials_in_output(self):
        self.client.login.side_effect = ftplib.error_perm(f"530 {' '.join(self.credentials.values())}")
        with redirect_stdout(io.StringIO()) as stdout, redirect_stderr(io.StringIO()) as stderr:
            with self.assertRaises(SystemExit) as failure:
                main()

        output = stdout.getvalue() + stderr.getvalue() + str(failure.exception)
        for value in self.credentials.values():
            self.assertNotIn(value, output)
        self.client.cwd.assert_not_called()
        self.tls_ftp.assert_called_once()
        self.plain_ftp.assert_not_called()


class FTPSRetryTest(unittest.TestCase):
    def setUp(self):
        self.sleep = self.enterContext(patch("deploy_ftp.time.sleep"))
        self.connect = self.enterContext(patch("deploy_ftp.ftplib.FTP_TLS"))
        self.output = self.enterContext(patch("builtins.print"))
        self.session = FTPSession(ssl.SSLContext(ssl.PROTOCOL_TLS_CLIENT), "fixture.invalid", "user", "password")
        self.addCleanup(self.session.close)

    def test_timeout_or_temporary_failure_reconnects_and_retries_operation(self):
        clients = [Mock(), Mock(), Mock()]
        self.connect.side_effect = clients
        operation = Mock(side_effect=[TimeoutError("fixture private reply"), ftplib.error_temp("426 private reply"), True])
        self.assertTrue(self.session.run(operation))
        self.assertEqual(operation.call_count, 3)
        for attempted, client in zip(operation.call_args_list, clients, strict=True):
            self.assertIs(attempted.args[0], client)
        for client in clients[:2]:
            client.close.assert_called_once_with()
            client.quit.assert_not_called()
        self.assertEqual(self.sleep.call_args_list, [call(1), call(2)])
        for message in self.output.call_args_list:
            self.assertNotIn("private reply", message.args[0])
            self.assertTrue(message.kwargs["flush"])

    def test_retry_restarts_partial_upload_from_first_byte(self):
        destination = REMOTE_PUBLIC / "index.php"
        content = b"complete replacement"
        remote = {}
        dropped = Mock()
        recovered = Mock()
        self.connect.side_effect = [dropped, recovered]
        dropped.size.return_value = None
        recovered.size.side_effect = lambda name: len(remote[name])

        def partial_upload(command, file):
            remote[destination.as_posix()] = file.read(5)
            raise ConnectionResetError()

        def completed_upload(command, file):
            self.assertEqual(command, "STOR /htdocs/index.php")
            remote[destination.as_posix()] = file.read()

        dropped.storbinary.side_effect = partial_upload
        recovered.storbinary.side_effect = completed_upload
        with tempfile.TemporaryDirectory() as directory:
            file = Path(directory) / "index.php"
            file.write_bytes(content)
            result = self.session.run(
                lambda ftp, available: upload_file(ftp, available, destination, file, len(content), True)
            )
        self.assertFalse(result)
        self.assertEqual(remote[destination.as_posix()], content)
        dropped.close.assert_called_once_with()
        recovered.retrbinary.assert_not_called()
        self.sleep.assert_called_once_with(1)

    def test_timeout_exhausts_retry_budget_and_closes_last_connection(self):
        clients = [Mock(), Mock(), Mock()]
        self.connect.side_effect = clients
        for client in clients:
            client.connect.side_effect = TimeoutError()
        operation = Mock()
        with self.assertRaises(TimeoutError):
            self.session.run(operation)
        self.assertEqual(self.connect.call_count, 3)
        operation.assert_not_called()
        for client in clients:
            client.close.assert_called_once_with()

    def test_tls_and_permanent_transfer_errors_are_never_retried(self):
        for error in (ssl.SSLError("TLS failed"), ftplib.error_perm("553 upload denied")):
            with self.subTest(error=type(error).__name__):
                self.connect.reset_mock()
                operation = Mock(side_effect=error)
                with self.assertRaises(type(error)):
                    self.session.run(operation)
                self.connect.assert_called_once()
                operation.assert_called_once()
        self.sleep.assert_not_called()

    def test_temporary_login_rejection_does_not_retry_authentication(self):
        self.connect.return_value.login.side_effect = ftplib.error_temp("421 login unavailable")
        operation = Mock()
        with self.assertRaises(ftplib.Error):
            self.session.run(operation)
        self.connect.assert_called_once()
        operation.assert_not_called()
        self.sleep.assert_not_called()


class ProgressTest(unittest.TestCase):
    def test_progress_reports_every_hundred_files_and_on_heartbeat(self):
        with patch("deploy_ftp.threading.Thread") as thread, patch("deploy_ftp.time.monotonic") as clock, patch("builtins.print") as log:
            clock.return_value = 0
            with Progress(101, 505) as progress:
                self.assertIn("0/101 files, 0/505 bytes", log.call_args.args[0])
                for _ in range(99):
                    progress.complete_file(5, verified=True)
                log.assert_called_once()
                clock.return_value = 10
                progress.complete_file(5, verified=False)
                report = log.call_args.args[0]
                self.assertIn("100/101 files, 500/505 bytes", report)
                self.assertIn("99 verified without upload, 5 bytes uploaded", report)
                self.assertIn("0.5 uploaded bytes/s average, elapsed 10.0s", report)
                clock.return_value = 30
                with patch.object(progress.stopped, "wait", side_effect=[False, True]) as wait:
                    thread.call_args.kwargs["target"]()
                self.assertEqual(wait.call_args_list, [call(30), call(30)])
                self.assertIn("100/101 files", log.call_args.args[0])
                self.assertIn("elapsed 30.0s", log.call_args.args[0])
                progress.complete_file(5, verified=False)
            self.assertTrue(all(entry.kwargs["flush"] for entry in log.call_args_list))
            self.assertIn("completed: 101/101 files, 505/505 bytes", log.call_args.args[0])


class DeployFTPTest(unittest.TestCase):
    def setUp(self):
        temporary = tempfile.TemporaryDirectory()
        self.addCleanup(temporary.cleanup)
        self.source = Path(temporary.name)
        self.package = self.source / "deploy"
        self.write_source()

    def write_source(self):
        files = {
            "app/Release.php": "release 1",
            "app/Retired.php": "retired code",
            "bootstrap/app.php": "<?php",
            "config/app.php": "<?php",
            "database/factories/UserFactory.php": "<?php",
            "database/migrations/create_users.php": "<?php",
            "database/seeders/DatabaseSeeder.php": "<?php",
            "resources/views/login.blade.php": "login",
            "routes/web.php": "<?php",
            "vendor/autoload.php": "autoload 1",
            "artisan": "<?php",
            "composer.json": "{}",
            "composer.lock": "{}",
            "public/.htaccess": "RewriteEngine On",
            "public/favicon.ico": "favicon",
            "public/images/sekolah.jpg": "school image",
            "public/index.php": """<?php
file_exists(__DIR__.'/../storage/framework/maintenance.php');
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->handleRequest($request);
""",
            "public/robots.txt": "User-agent: *",
            "public/sw.js": 'define(["./workbox-example"], () => [{url:"build/assets/app-abcdefgh.js"}]);',
            "public/workbox-example.js": "workbox",
            "public/build/assets/app-abcdefgh.js": "asset 1",
            "public/build/manifest.json": '{"entry.js":{"file":"assets/app-abcdefgh.js"}}',
            ".env": "root fixture secret",
            "app/.ENV.production": "app fixture secret",
            "vendor/auth.json": "fixture credentials",
            "config/service.pem": "fixture private key",
            "database/attendance.db": "fixture database",
            "database/migrations/attendance.sqlite3-wal": "fixture database journal",
            "bootstrap/cache/config.php": "fixture cached config",
            "bootstrap/cache/nested/runtime.txt": "fixture runtime cache",
            "storage/app/private/form.pdf": "fixture private upload",
            "public/storage/form.pdf": "fixture public upload",
            "public/images/student.jpg": "unlisted public upload",
            "public/uploads/photo.jpg": "fixture public upload",
            "public/workbox-retired.js": "old workbox",
        }
        icons = []
        for name, size, purpose in (
            ("pwa-192x192.png", 192, "any"), ("pwa-512x512.png", 512, "any"),
            ("pwa-maskable-512x512.png", 512, "maskable"),
        ):
            icons.append({"src": f"/{name}", "sizes": f"{size}x{size}", "type": "image/png", "purpose": purpose})
            files[f"public/{name}"] = b"\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR" + struct.pack(">II", size, size)
        files["public/manifest.webmanifest"] = json.dumps(
            {"start_url": "/", "scope": "/", "display": "standalone", "icons": icons}
        )
        for name, content in files.items():
            write_file(self.source, name, content)

    def build_package(self):
        result = subprocess.run([sys.executable, "-B", str(PACKAGER)], cwd=self.source, capture_output=True, text=True)
        self.assertEqual(result.returncode, 0, result.stdout + result.stderr)
        return deployment_files(self.package)

    def test_packager_omits_secrets_runtime_uploads_and_databases(self):
        app, public = self.build_package()
        self.assertIn(PurePosixPath("database/migrations/create_users.php"), app)
        self.assertIn(PurePosixPath("workbox-example.js"), public)
        for path in (
            "app/.env", "app/app/.ENV.production", "app/vendor/auth.json", "app/config/service.pem",
            "app/database/attendance.db", "app/database/migrations/attendance.sqlite3-wal",
            "app/bootstrap/cache", "app/storage", "public/storage", "public/uploads",
            "public/images/student.jpg", "public/workbox-retired.js",
        ):
            self.assertFalse((self.package / path).exists(), path)

    def test_preflight_rejects_protected_files_in_an_artifact(self):
        self.build_package()
        for name in (
            "app/.env", "app/config/auth.json", "app/database/roster.db", "app/bootstrap/cache/config.php",
            "app/storage/app/private/receipt.pdf", "public/storage/photo.jpg", "public/images/upload.jpg",
        ):
            with self.subTest(name=name):
                write_file(self.package, name, "must not upload")
                with self.assertRaises(ValueError):
                    deployment_files(self.package)
                (self.package / name).unlink()

    def test_packager_rejects_missing_vite_asset_even_outside_precache(self):
        write_file(
            self.source, "public/build/manifest.json",
            '{"entry.css":{"file":"assets/entry-abcdefgh.css"}}',
        )
        result = subprocess.run([sys.executable, "-B", str(PACKAGER)], cwd=self.source, capture_output=True, text=True)
        self.assertNotEqual(result.returncode, 0)
        self.assertIn("Invalid Vite asset path", result.stderr)

    def test_first_upload_and_repeated_uploads_preserve_server_data(self):
        app, public = self.build_package()
        persistent = {
            "/htdocs/laravel_app/.env": b"prod env fixture",
            "/htdocs/laravel_app/storage/app/private/receipt.pdf": b"private upload",
            "/htdocs/laravel_app/storage/framework/sessions/session": b"session",
            "/htdocs/laravel_app/bootstrap/cache/config.php": b"prod cache",
            "/htdocs/laravel_app/database/attendance.db": b"prod database",
            "/htdocs/laravel_app/database/attendance.sqlite3-wal": b"prod database journal",
            "/htdocs/storage/avatar.jpg": b"public storage",
            "/htdocs/images/student.jpg": b"unmanaged image",
            "/htdocs/.ftp-deploy-sync-state.json": b"old sync state",
        }
        ftp = SimulatedFTP(persistent)
        upload_packages(ftp, self.package, app, public)
        self.assertIn("/htdocs/laravel_app/storage/logs", ftp.created)
        self.assertEqual(ftp.files["/htdocs/laravel_app/app/Release.php"], b"release 1")
        asset = "/htdocs/build/assets/app-abcdefgh.js"
        for entry in ("/htdocs/build/manifest.json", "/htdocs/sw.js", "/htdocs/index.php"):
            self.assertLess(ftp.uploaded.index(asset), ftp.uploaded.index(entry))
        self.assertLess(ftp.uploaded.index("/htdocs/workbox-example.js"), ftp.uploaded.index("/htdocs/sw.js"))

        write_file(self.package, "app/app/Release.php", "release 2")
        write_file(self.package, "app/vendor/autoload.php", "autoload 2")
        write_file(self.package, "public/build/assets/app-abcdefgh.js", "asset 2")
        (self.package / "app/app/Retired.php").unlink()
        app, public = deployment_files(self.package)
        ftp.created.clear()
        upload_packages(ftp, self.package, app, public)
        self.assertEqual(ftp.created, [])
        self.assertEqual(ftp.files["/htdocs/laravel_app/app/Release.php"], b"release 2")
        self.assertEqual(ftp.files["/htdocs/laravel_app/vendor/autoload.php"], b"autoload 2")
        self.assertEqual(ftp.files[asset], b"asset 2")
        self.assertEqual(ftp.files["/htdocs/laravel_app/app/Retired.php"], b"retired code")
        for name, content in persistent.items():
            self.assertNotIn(name, ftp.uploaded)
            self.assertEqual(ftp.files[name], content)

    def test_resume_skips_only_verified_content_and_never_reads_persistent_paths(self):
        app, public = self.build_package()
        prefix = "/htdocs/laravel_app/"
        protected = prefix + ".env"
        identical = prefix + "app/Release.php"
        corrupt = prefix + "vendor/autoload.php"
        truncated = prefix + "app/Retired.php"
        ftp = SimulatedFTP({protected: b"fixture env", identical: b"release 1", corrupt: b"autoload X", truncated: b"short"})
        upload_packages(ftp, self.package, app, public, resume=True)
        self.assertIn(identical, ftp.retrieved)
        self.assertNotIn(identical, ftp.uploaded)
        self.assertIn(corrupt, ftp.retrieved)
        self.assertIn(corrupt, ftp.uploaded)
        self.assertNotIn(truncated, ftp.retrieved)
        self.assertIn(truncated, ftp.uploaded)
        self.assertEqual(ftp.files[corrupt], b"autoload 1")
        self.assertEqual(ftp.files[truncated], b"retired code")
        self.assertEqual(ftp.files[protected], b"fixture env")
        self.assertNotIn(protected, ftp.size_checked + ftp.retrieved + ftp.uploaded)
        self.assertEqual(ftp.uploaded[-1], "/htdocs/index.php")

    def test_resume_reuploads_when_remote_verification_is_unavailable(self):
        app, public = self.build_package()
        path = "/htdocs/laravel_app/app/Release.php"
        for method, reply in (("size", "502 SIZE unsupported"), ("retrbinary", "550 read unavailable")):
            with self.subTest(method=method):
                ftp = SimulatedFTP({path: b"release 1"})
                with patch.object(ftp, method, side_effect=ftplib.error_perm(reply)):
                    upload_packages(ftp, self.package, app, public, resume=True)
                self.assertIn(path, ftp.uploaded)
