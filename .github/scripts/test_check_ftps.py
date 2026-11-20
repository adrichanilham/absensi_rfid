import ftplib
import io
import os
import ssl
import unittest
from contextlib import redirect_stderr, redirect_stdout
from unittest.mock import ANY, call, patch

from check_ftps import main


class CheckFTPSTest(unittest.TestCase):
    def setUp(self):
        self.settings = {
            "FTP_SERVER": "ftpupload.net",
            "FTP_USERNAME": "test-only-username",
            "FTP_PASSWORD": "test-only-password",
        }
        self.enterContext(patch.dict(os.environ, self.settings))
        self.plain_ftp = self.enterContext(patch("check_ftps.ftplib.FTP"))
        self.tls_ftp = self.enterContext(patch("check_ftps.ftplib.FTP_TLS", autospec=True))
        self.connection = self.tls_ftp.return_value
        self.ftp = self.connection.__enter__.return_value
        self.stdout = self.enterContext(redirect_stdout(io.StringIO()))
        self.stderr = self.enterContext(redirect_stderr(io.StringIO()))
        self.addCleanup(self.plain_ftp.assert_not_called)

    def test_checks_verified_passive_data_channel_without_printing_listing(self):
        filename = "private-fixture-name.txt"
        self.ftp.retrlines.side_effect = lambda command, callback: callback(filename)

        main()

        self.tls_ftp.assert_called_once_with(context=ANY, timeout=30)
        context = self.tls_ftp.call_args.kwargs["context"]
        self.assertTrue(context.check_hostname)
        self.assertEqual(context.verify_mode, ssl.CERT_REQUIRED)
        self.assertEqual(context.minimum_version, ssl.TLSVersion.TLSv1_2)
        self.assertIsNone(context.keylog_filename)
        self.assertEqual(self.ftp.mock_calls, [
            call.connect("ftpupload.net", port=21),
            call.auth(),
            call.login(self.settings["FTP_USERNAME"], self.settings["FTP_PASSWORD"]),
            call.prot_p(),
            call.set_pasv(True),
            call.cwd("/htdocs"),
            call.retrlines("LIST", ANY),
        ])
        self.connection.__exit__.assert_called_once_with(None, None, None)
        output = self.stdout.getvalue() + self.stderr.getvalue()
        self.assertIn("listing succeeded", output)
        for value in (filename, self.settings["FTP_USERNAME"], self.settings["FTP_PASSWORD"]):
            self.assertNotIn(value, output)

    def test_refuses_another_host_before_connecting(self):
        with patch.dict(os.environ, FTP_SERVER="unexpected-host.invalid"):
            with self.assertRaisesRegex(SystemExit, "requires FTP_SERVER to be ftpupload.net"):
                main()
        self.tls_ftp.assert_not_called()

    def test_refuses_missing_credentials_before_connecting(self):
        with patch.dict(os.environ, FTP_PASSWORD=""):
            with self.assertRaisesRegex(SystemExit, "credentials are missing"):
                main()
        self.tls_ftp.assert_not_called()

    def test_certificate_failure_closes_connection_before_login(self):
        self.ftp.auth.side_effect = ssl.SSLCertVerificationError("untrusted certificate")
        with self.assertRaisesRegex(SystemExit, "certificate or hostname verification failed"):
            main()
        self.ftp.login.assert_not_called()
        self.ftp.retrlines.assert_not_called()
        self.connection.__exit__.assert_called_once()

    def test_rejected_data_protection_prevents_directory_access(self):
        self.ftp.prot_p.side_effect = ftplib.error_perm("534 protection unavailable")
        with self.assertRaisesRegex(SystemExit, "FTPS check failed"):
            main()
        self.ftp.cwd.assert_not_called()
        self.ftp.retrlines.assert_not_called()
        self.connection.__exit__.assert_called_once()

    def test_failure_does_not_print_sensitive_server_reply(self):
        sensitive = ("private-fixture-name.txt", self.settings["FTP_USERNAME"], self.settings["FTP_PASSWORD"])
        self.ftp.retrlines.side_effect = ftplib.error_perm(f"550 {' '.join(sensitive)}")
        with self.assertRaisesRegex(SystemExit, "FTPS check failed") as failure:
            main()
        output = self.stdout.getvalue() + self.stderr.getvalue() + str(failure.exception)
        for value in sensitive:
            self.assertNotIn(value, output)
        self.connection.__exit__.assert_called_once()
