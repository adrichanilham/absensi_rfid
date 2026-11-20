import argparse
import json
import re
import shutil
import struct
from pathlib import Path


def private_file(name):
    return (
        name == ".env"
        or name.startswith(".env.")
        or name in {"auth.json", "composer.json", "composer.lock"}
        or name.endswith((".sqlite", ".sqlite3", ".key"))
    )


def require_file(path):
    if not path.is_file() or path.stat().st_size == 0:
        raise SystemExit(f"Missing or empty deployment file: {path}")


def validate_pwa(public):
    for path in (".htaccess", "index.php", "build/manifest.json", "images/sekolah.jpg"):
        require_file(public / path)

    if (public / "build/manifest.webmanifest").exists():
        raise SystemExit("Duplicate PWA manifest under build/")

    manifest = json.loads((public / "manifest.webmanifest").read_text(encoding="utf-8"))
    if (manifest["start_url"], manifest["scope"], manifest["display"]) != ("/", "/", "standalone"):
        raise SystemExit("Unexpected PWA start URL, scope, or display mode")

    expected_icons = {
        "/pwa-192x192.png": (192, "any"),
        "/pwa-512x512.png": (512, "any"),
        "/pwa-maskable-512x512.png": (512, "maskable"),
    }
    icons = manifest["icons"]
    if len(icons) != len(expected_icons) or {icon["src"] for icon in icons} != expected_icons.keys():
        raise SystemExit("Unexpected icons in the PWA manifest")
    for icon in icons:
        size, purpose = expected_icons[icon["src"]]
        with (public / icon["src"].lstrip("/")).open("rb") as file:
            header = file.read(24)
        if (
            len(header) != 24
            or header[:16] != b"\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR"
            or struct.unpack(">II", header[16:]) != (size, size)
            or (icon["sizes"], icon["type"], icon["purpose"]) != (f"{size}x{size}", "image/png", purpose)
        ):
            raise SystemExit(f"Invalid deployment icon: {icon['src']}")

    worker = (public / "sw.js").read_text(encoding="utf-8")
    workbox = re.search(r'"\./(workbox-[\w-]+)"', worker)
    if workbox is None:
        raise SystemExit("Unable to determine the Workbox file from sw.js")
    require_file(public / f"{workbox[1]}.js")

    precache = re.findall(r'\burl:\s*"([^"]+)"', worker)
    if not precache or len(precache) != len(set(precache)):
        raise SystemExit("Missing or duplicate Workbox precache URLs")
    for url in precache:
        asset = url.removeprefix("/")
        if f"/{asset}" not in expected_icons and not re.fullmatch(
            r"build/assets/(?:[\w.-]+/)*[\w.-]+\.(?:css|js|woff2)", asset
        ):
            raise SystemExit(f"Unexpected Workbox precache URL: {url}")
        if {"..", "."}.intersection(asset.split("/")) or not (public / asset).is_file():
            raise SystemExit(f"Invalid Workbox precache path: {url}")
    print(f"Validated {len(precache)} precache URLs, {workbox[1]}.js and {len(icons)} PWA icons.")


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("output", nargs="?", type=Path, default=Path("deploy"))
    output = parser.parse_args().output.resolve()
    output.mkdir(parents=True, exist_ok=False)
    app, public = output / "app", output / "public"
    app.mkdir()

    for directory in ("app", "bootstrap", "config", "database", "resources", "routes", "vendor"):
        shutil.copytree(directory, app / directory, ignore=shutil.ignore_patterns(
            ".env", ".env.*", "auth.json", "*.sqlite", "*.sqlite3", "database.sqlite-*"
        ))
    for file in ("artisan", "composer.json", "composer.lock"):
        shutil.copy2(file, app / file)
    for directory in ("bootstrap/cache", "storage/app/private", "storage/app/public",
                      "storage/framework/cache/data", "storage/framework/sessions",
                      "storage/framework/views", "storage/logs"):
        (app / directory).mkdir(parents=True, exist_ok=True)
    for file in (app / "bootstrap/cache").glob("*.php"):
        file.unlink()
    (app / ".htaccess").write_text("Require all denied\n", encoding="utf-8")

    shutil.copytree("public", public, symlinks=True, ignore=shutil.ignore_patterns(
        "hot", "fonts-manifest.dev.json", "storage"
    ))
    for path in public.rglob("*"):
        if path.is_symlink() or private_file(path.name.lower()) or path.name.lower() == "laravel_app":
            raise SystemExit(f"Unexpected file or directory in public deployment package: {path}")

    index = public / "index.php"
    entrypoint = index.read_text(encoding="utf-8")
    for directory in ("storage", "vendor", "bootstrap"):
        original = f"__DIR__.'/../{directory}/"
        if entrypoint.count(original) != 1:
            raise SystemExit(f"Unable to relocate the Laravel {directory} path")
        entrypoint = entrypoint.replace(original, f"__DIR__.'/laravel_app/{directory}/")
    handle_request = "$app->handleRequest("
    if entrypoint.count(handle_request) != 1:
        raise SystemExit("Unable to set the Laravel public path")
    index.write_text(
        entrypoint.replace(handle_request, f"$app->usePublicPath(__DIR__);\n\n{handle_request}"),
        encoding="utf-8",
    )

    validate_pwa(public)


if __name__ == "__main__":
    main()
