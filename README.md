<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Deploy ke InfinityFree

Workflow GitHub Actions di `.github/workflows/deploy.yml` menjalankan test, membangun asset, lalu mengirim aplikasi melalui FTP setiap ada push ke branch `main`. Deploy manual juga dapat dijalankan dari tab **Actions** dengan workflow **Laravel 13 CI/CD - InfinityFree**.

### Persiapan hosting

1. Pastikan paket InfinityFree menyediakan PHP 8.3 atau lebih baru dan ekstensi `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`, `tokenizer`, `xml`, dan `ctype`.
2. Buat database MySQL dari panel InfinityFree dan catat hostname, nama database, username, dan password pada bagian **MySQL Databases**.
3. Di root FTP akun, siapkan dua direktori sejajar: `htdocs` dan `laravel_app`. Workflow mengirim berkas web ke `htdocs/` dan kode Laravel (termasuk `vendor/`) ke `laravel_app/`. Jangan letakkan `.env` di `htdocs`.
4. Di GitHub, buka **Settings → Secrets and variables → Actions → New repository secret**, lalu tambahkan:

   | Secret | Nilai |
   | --- | --- |
   | `FTP_SERVER` | Host FTP yang ditampilkan InfinityFree (tanpa `ftp://`) |
   | `FTP_USERNAME` | Username FTP |
   | `FTP_PASSWORD` | Password FTP |

5. Upload berkas `.env` ke `laravel_app/.env` melalui FTP atau File Manager. Isi konfigurasi minimum berikut memakai detail database dari panel:

   ```dotenv
   APP_NAME="Absensi RFID"
   APP_ENV=production
   APP_KEY=base64:GANTI_DENGAN_KEY_YANG_DIBUAT_SECARA_LOKAL
   APP_DEBUG=false
   APP_URL=https://domain-anda

   DB_CONNECTION=mysql
   DB_HOST=hostname-dari-panel
   DB_PORT=3306
   DB_DATABASE=nama-database-dari-panel
   DB_USERNAME=username-dari-panel
   DB_PASSWORD=password-dari-panel

   SESSION_DRIVER=file
   CACHE_STORE=file
   QUEUE_CONNECTION=sync
   LOG_CHANNEL=single
   LOG_LEVEL=error
   ```

   Buat key sekali di komputer lokal dengan `php artisan key:generate --show`, lalu salin hasilnya sebagai nilai `APP_KEY`. Simpan `.env` hanya di hosting; jangan commit atau masukkan isinya ke log GitHub Actions. Pastikan direktori `storage/` dan `bootstrap/cache/` dapat ditulis oleh PHP.

### Database dan deploy berikutnya

InfinityFree dengan FTP saja tidak menyediakan akses Artisan/SSH ke server, sehingga workflow **tidak menjalankan migrasi database secara otomatis**. Siapkan skema tabel aplikasi melalui phpMyAdmin sebelum aplikasi dipakai, dan terapkan migrasi baru dengan metode yang didukung hosting. Jangan gunakan file `database/database.sqlite` lokal sebagai database produksi; workflow mengecualikannya dari upload.

Setelah secret, folder, `.env`, dan database siap, push perubahan ke `main` atau jalankan workflow manual dari GitHub Actions. Periksa log workflow sampai kedua langkah deploy FTP berhasil. `.env` dan `APP_KEY` yang dibuat di server tetap tersimpan saat deploy.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
