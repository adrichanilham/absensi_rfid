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
from unittest.mock import call, patch

from deploy_ftp import deployment_files, main, upload_packages


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
        self.client = self.tls_ftp.return_value.__enter__.return_value
        self.upload = self.enterContext(patch("deploy_ftp.upload_packages"))

    def test_explicit_tls_protects_control_and_passive_data_before_upload(self):
        with redirect_stdout(io.StringIO()) as stdout, redirect_stderr(io.StringIO()) as stderr:
            main()

        self.tls_ftp.assert_called_once()
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
        ])
        self.upload.assert_called_once_with(self.client, Path("deploy"), [], [])
        self.plain_ftp.assert_not_called()
        for value in self.credentials.values():
            self.assertNotIn(value, stdout.getvalue() + stderr.getvalue())

    def test_certificate_verification_failure_stops_before_login_without_fallback(self):
        self.client.auth.side_effect = ssl.SSLCertVerificationError("certificate mismatch")
        with self.assertRaisesRegex(SystemExit, "FTPS certificate or hostname verification failed"):
            main()

        self.client.login.assert_not_called()
        self.client.prot_p.assert_not_called()
        self.upload.assert_not_called()
        self.plain_ftp.assert_not_called()

    def test_auth_tls_rejection_stops_before_login_without_fallback(self):
        self.client.auth.side_effect = ftplib.error_perm("502 AUTH TLS unavailable")
        with self.assertRaisesRegex(SystemExit, "FTPS connection, login or transfer failed"):
            main()

        self.client.login.assert_not_called()
        self.upload.assert_not_called()
        self.plain_ftp.assert_not_called()

    def test_data_protection_rejection_prevents_upload_without_fallback(self):
        self.client.prot_p.side_effect = ftplib.error_perm("534 PROT P rejected")
        with self.assertRaisesRegex(SystemExit, "FTPS connection, login or transfer failed"):
            main()

        self.upload.assert_not_called()
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
        self.upload.assert_not_called()
        self.plain_ftp.assert_not_called()


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
