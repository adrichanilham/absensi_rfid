import argparse
import json
import re
import shutil
import struct
from pathlib import Path, PurePosixPath

from deploy_ftp import (
    APP_DIRECTORIES,
    DATABASE_DIRECTORIES,
    PUBLIC_FILES,
    deployment_files,
    excluded_name,
    package_files,
    public_file_allowed,
)


def ignore_private_files(source, names):
    return [
        name for name in names
        if excluded_name(name) or (Path(source) == Path("bootstrap") and name.lower() == "cache")
    ]


def copy_file(source, destination):
    if any(path.is_symlink() for path in (source, *source.parents)):
        raise SystemExit(f"Unexpected symbolic link in deployment source: {source}")
    destination.parent.mkdir(parents=True, exist_ok=True)
    shutil.copy2(source, destination)


def require_file(path):
    if not path.is_file() or path.stat().st_size == 0:
        raise SystemExit(f"Missing or empty deployment file: {path}")


def workbox_file(worker):
    workbox = re.search(r'"\./(workbox-[\w-]+)"', worker)
    if workbox is None:
        raise SystemExit("Unable to determine the Workbox file from sw.js")
    return f"{workbox[1]}.js"


def validate_pwa(public):
    for path in (".htaccess", "index.php", "build/manifest.json", "images/sekolah.jpg"):
        require_file(public / path)

    if (public / "build/manifest.webmanifest").exists():
        raise SystemExit("Duplicate PWA manifest under build/")

    vite_manifest = json.loads((public / "build/manifest.json").read_text(encoding="utf-8"))
    for entry in vite_manifest.values():
        for asset in (entry["file"], *entry.get("css", []), *entry.get("assets", [])):
            path = PurePosixPath("build") / asset
            if ".." in path.parts or not public_file_allowed(path) or not (public / path).is_file():
                raise SystemExit(f"Invalid Vite asset path: {asset}")

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
    workbox = workbox_file(worker)
    require_file(public / workbox)

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
    print(f"Validated {len(precache)} precache URLs, {workbox} and {len(icons)} PWA icons.")


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("output", nargs="?", type=Path, default=Path("deploy"))
    output = parser.parse_args().output.resolve()
    output.mkdir(parents=True, exist_ok=False)
    app, public = output / "app", output / "public"
    app.mkdir()

    for directory in (*APP_DIRECTORIES, *(f"database/{name}" for name in DATABASE_DIRECTORIES)):
        source = Path(directory)
        if any(path.is_symlink() for path in (source, *source.parents)):
            raise SystemExit(f"Unexpected symbolic link in deployment source: {source}")
        shutil.copytree(source, app / directory, symlinks=True, ignore=ignore_private_files)
    for file in ("artisan", "composer.json", "composer.lock"):
        copy_file(Path(file), app / file)
    (app / ".htaccess").write_text("Require all denied\n", encoding="utf-8")

    public_source = Path("public")
    for file in PUBLIC_FILES:
        copy_file(public_source / file, public / file)
    worker = (public / "sw.js").read_text(encoding="utf-8")
    workbox = workbox_file(worker)
    copy_file(public_source / workbox, public / workbox)
    build = PurePosixPath("build")
    for file in package_files(public_source / build, lambda path: public_file_allowed(build / path)):
        copy_file(public_source / build / file, public / build / file)

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
    deployment_files(output)


if __name__ == "__main__":
    main()
