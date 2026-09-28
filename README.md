# Juice Box API

Laravel 12 REST API dengan token authentication (Sanctum), Posts CRUD, weather lookup, dan queued background jobs.

**[English](#english)** | **[Bahasa Indonesia](#bahasa-indonesia)**

---

## English

### Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### Environment Variables

**Database**: MySQL (default) or SQLite (`DB_CONNECTION=sqlite`, no other DB_* needed).

**Weather API (OpenWeatherMap)**, required for `/api/weather`:
1. Get a free API key at [openweathermap.org/api](https://openweathermap.org/api).
2. Add to `.env`:
   ```env
   OPENWEATHERMAP_API_KEY=your_api_key_here
   ```

**Mail (Mailtrap)**, required to actually receive the welcome email:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
```
Get these from your Mailtrap inbox, then SMTP Settings, then pick the "Laravel" preset. Without this, emails just get written to `storage/logs/laravel.log` instead of being sent.

### Background Jobs

Registering a user queues a welcome email, and the weather cache refreshes automatically every 15 minutes. Both need a queue worker running, and the weather refresh also needs the scheduler:

```bash
php artisan queue:work      # processes queued jobs
php artisan schedule:work   # triggers scheduled tasks (dev only, use a cron entry in production)
```

To manually dispatch a welcome email for testing (without going through the registration flow), run:

```bash
php artisan email:send-welcome user@example.com
```

This queues the job for that user; `queue:work` still needs to be running for it to actually be processed and sent.

### Testing

```bash
php artisan test
```
Uses an in-memory SQLite database and mocks the weather API (`Http::fake()`), so there are no real network calls and your real database stays untouched.

### API Reference

Base URL: `http://<your-app-url>/api`. Auth: `Authorization: Bearer <access_token>` (obtained from Register/Login).

Response shape: `{ "success": bool, "message": string, "data"/"errors": ... }`

| Folder | Method | Endpoint | Auth | Description |
|---|---|---|---|---|
| **User & Login** | GET | `/users/{id}` | ✅ | Get a specific user |
| | POST | `/register` | ❌ | Register, returns access token |
| | POST | `/login` | ❌ | Login, returns access token |
| | POST | `/logout` | ✅ | Revoke current token |
| | GET | `/user` | ✅ | Check token / get current user |
| **Post** | GET | `/posts` | ✅ | List posts (paginated) |
| | GET | `/posts/{id}` | ✅ | Get one post |
| | POST | `/posts` | ✅ | Create post (`title`, `content`) |
| | PUT/PATCH | `/posts/{id}` | ✅ owner only | Update post |
| | DELETE | `/posts/{id}` | ✅ owner only | Delete post |
| **Weather** | GET | `/weather?city=` | ✅ | Current weather (defaults to Perth, Australia) |

### Using the Postman Collection

1. Import the collection and its environment into Postman.
2. Select the environment (top-right dropdown).
3. Run **Login** or **Register** first. Its script auto-saves the token into the `access_token` variable.
4. All other requests already use `{{access_token}}`, so they work right after.

---

## Bahasa Indonesia

### Cara Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### Environment Variables

**Database**: MySQL (default) atau SQLite (`DB_CONNECTION=sqlite`, gak perlu isi DB_* lain).

**Weather API (OpenWeatherMap)**, wajib buat endpoint `/api/weather`:
1. Daftar API key gratis di [openweathermap.org/api](https://openweathermap.org/api).
2. Tambahin ke `.env`:
   ```env
   OPENWEATHERMAP_API_KEY=api_key_kamu
   ```

**Mail (Mailtrap)**, wajib biar welcome email beneran kekirim:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=username_mailtrap_kamu
MAIL_PASSWORD=password_mailtrap_kamu
```
Ambil dari inbox Mailtrap, buka SMTP Settings, pilih preset "Laravel". Kalau belum diisi, email cuma nyangkut di `storage/logs/laravel.log`, gak beneran terkirim.

### Background Jobs

Register user itu ngirim welcome email lewat antrean, dan data cuaca di-refresh otomatis tiap 15 menit. Dua-duanya butuh queue worker jalan, dan refresh cuaca juga butuh scheduler:

```bash
php artisan queue:work      # proses job yang ada di antrean
php artisan schedule:work   # jalanin tugas terjadwal (khusus dev, pakai cron kalau production)
```

Buat ngetes kirim welcome email secara manual (tanpa perlu daftar user beneran lewat endpoint register), jalanin:

```bash
php artisan email:send-welcome user@example.com
```

Ini bakal masukin job-nya ke antrean; `queue:work` tetap harus jalan biar job-nya beneran diproses dan email-nya terkirim.

### Testing

```bash
php artisan test
```
Pakai database SQLite sementara (in-memory) dan API cuaca di-mock (`Http::fake()`), jadi gak ada request beneran ke internet dan database asli kamu gak kesentuh.

### Referensi API

Base URL: `http://<url-app-kamu>/api`. Auth: `Authorization: Bearer <access_token>` (didapat dari Register/Login).

Bentuk response: `{ "success": bool, "message": string, "data"/"errors": ... }`

| Folder | Method | Endpoint | Auth | Keterangan |
|---|---|---|---|---|
| **User & Login** | GET | `/users/{id}` | ✅ | Lihat data 1 user |
| | POST | `/register` | ❌ | Daftar, dapat access token |
| | POST | `/login` | ❌ | Login, dapat access token |
| | POST | `/logout` | ✅ | Hapus token yang lagi dipakai |
| | GET | `/user` | ✅ | Cek token / lihat user yang login |
| **Post** | GET | `/posts` | ✅ | Daftar semua post (paginated) |
| | GET | `/posts/{id}` | ✅ | Lihat 1 post |
| | POST | `/posts` | ✅ | Bikin post (`title`, `content`) |
| | PUT/PATCH | `/posts/{id}` | ✅ cuma pemilik | Edit post |
| | DELETE | `/posts/{id}` | ✅ cuma pemilik | Hapus post |
| **Weather** | GET | `/weather?city=` | ✅ | Cuaca terkini (default Perth, Australia) |

### Cara Pakai Postman Collection

1. Import collection & environment-nya ke Postman.
2. Pilih environment-nya (dropdown kanan atas).
3. Jalanin **Login** atau **Register** dulu. Script-nya otomatis nyimpen token ke variable `access_token`.
4. Request lain udah pakai `{{access_token}}`, jadi langsung bisa dipakai setelahnya.
