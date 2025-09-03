# 📦 Laravel Upload Service

A backend service built with **Laravel 12**, providing secure file uploads with **token-based authentication**, **upload limits**, and **temporary download URLs**.  
The project can run inside Docker using **Nginx**, **PHP-FPM**, and **SQLite**.

---

## Features

- **Authentication** – User registration & login with Laravel Sanctum tokens
- **Upload Tokens** – Generate tokens with expiry, max file size, and status (`pending`, `uploaded`, `expired`)
- **File Uploads** – Upload via multipart file or base64 + checksum validation
- **List Uploads** – Retrieve a user’s upload history
- **Temporary Signed URLs** – Secure, time-limited (5 minutes) download links
- **Rate Limits** – Max 5 tokens per user per minute, file size limit of 50 MB
- **Background Jobs** – Automatic token expiry via scheduled job (runs every 10 minutes)
- **Dockerized** – Clean environment with Nginx + PHP-FPM + SQLite
- **Localization** – Apply localization for messages english & arabic (English default) - (need enhance for all messages)
- **Testing** – Apply feature testing on Auth Apis - (need enhance for all apis)
- **APIs Documentation** – postman collection for api documentation file is included in root folder

---

## ⚙️ Installation & Run (Docker)
### 1- 🛠 Prerequisites 
Download and install docker and docker-compose

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

### 2. Clone repository and run docker compose up
```bash
### 1. Clone repository
git clone https://github.com/your-username/upload-service.git
cd upload-service

### 2. Copy .env file
cp .env.example .env

### 3. Build and start containers
docker-compose up --build -d
```

App will be available at:
👉 [http://localhost:9090](http://localhost:8000)

## ⚙️ Installation & Run (Manual)

### Clone repository and manual run
```bash
### 1. Clone repository
git clone https://github.com/your-username/upload-service.git
cd upload-service

### 2. Copy .env file
cp .env.example .env

### 3- Install dependencies
composer install

### 4- Run artisan server
php artisan serve --port=9090

### 5- Run queue worker (if needed)
php artisan queue:work

### 6- Run scheduler (if needed)
php artisan schedule:work
```

App will be available at:
👉 [http://localhost:9090](http://localhost:8000)

---

## 🔑 API Endpoints
use can see all APIs Documentation on postman collection file in the root of the project

`File Upload Service - W88.postman_collection.json` you can import it on postman
### Authentication
- `POST /api/auth/register` → Register new user
- `POST /api/auth/login` → Login and receive token

### Uploads
- `POST /api/uploads` → Create upload token
- `POST /api/uploads/{token}` → Upload file (static file or base64 + checksum)
- `GET /api/uploads` → List user uploads

### Notes
- Include `Authorization: Bearer <token>` header for protected endpoints.
- File size limit: **50 MB** (configured in `config/filesystems.php`).
- Rate limit: **5 tokens per minute per user**.
- Temporary Signed Download URLs: (5 minutes) download links

---

##  Scheduler & Jobs

A background job automatically marks expired upload tokens every 10 minutes.  
To test manually:

```bash
php artisan queue:work
php artisan schedule:run
```

---

## Running Tests

Inside the container:
```bash
php artisan test
```

---

## 🗂 Project Structure

```
.
├── app/
│   ├── Http/               # API controllers & Requests
│   ├── Jobs/               # Background jobs
│   ├── Models/             # Eloquent models
│   └── Services/           # Bussiness Logic
├── database/               # SQLite DB & migration files
├── lang/                   # Localization Files
├── public/                 # Public assets
├── storage/                # Local Storage
├── routes/
│   ├── api.php             # API routes
│   └── console.php         # Schduled jobs
├── docker-compose.yml      # Docker compose services
├── docker                  # Dockerfile & Nginx config
├── ......
└── README.md
```
