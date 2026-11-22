import ftplib
import os
import ssl


SERVER = "ftpupload.net"


def main():
    if os.environ.get("FTP_SERVER") != SERVER:
        raise SystemExit("FTPS check requires FTP_SERVER to be ftpupload.net.")
    username = os.environ.get("FTP_USERNAME")
    password = os.environ.get("FTP_PASSWORD")
    if not username or not password:
        raise SystemExit("FTPS credentials are missing.")

    try:
        context = ssl.SSLContext(ssl.PROTOCOL_TLS_CLIENT)
        context.load_default_certs(ssl.Purpose.SERVER_AUTH)
        context.minimum_version = ssl.TLSVersion.TLSv1_2

        with ftplib.FTP_TLS(context=context, timeout=30) as ftp:
            ftp.connect(SERVER, port=21)
            ftp.auth()
            ftp.login(username, password)
            ftp.prot_p()
            ftp.set_pasv(True)
            ftp.cwd("/htdocs")
            # Exercise the protected data channel without logging directory entries.
            ftp.retrlines("LIST", lambda _: None)
    except ssl.SSLCertVerificationError:
        raise SystemExit("FTPS certificate or hostname verification failed.") from None
    except (ValueError, OSError, EOFError, ftplib.Error):
        raise SystemExit("FTPS check failed during connection, login or directory listing.") from None

    print("FTPS login and protected passive directory listing succeeded.")


if __name__ == "__main__":
    main()
