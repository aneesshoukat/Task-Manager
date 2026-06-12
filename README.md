# Task Manager

A feature-complete task management web application built with **native PHP 8.5** (no framework), MySQL, and Bootstrap 5.

## What It Does

- **User authentication** — Register, login, logout using JWT access + refresh tokens stored in HttpOnly cookies
- **Task management** — Create, edit, delete, view, mark complete, restore from trash, add comments
- **Search & filter** — Filter by status, priority, date range; search by title; sort by date or priority; paginated results
- **Dashboard** — See total, completed, pending, and overdue task counts with completion percentage
- **Profile** — Edit name/email, change password, upload avatar image
- **CSV import/export** — Download tasks as CSV or bulk import from a CSV file
- **Dark mode** — Toggle light/dark theme (persisted in localStorage)
- **REST API** — Full JSON API with Bearer token auth for all operations (register, login, tasks CRUD, dashboard stats, profile)
- **Rate limiting** — Login throttling (5 attempts per 15 minutes) and API rate limiting
- **Activity logs** — All user actions are logged (login, logout, task create/update/delete, etc.)
- **Security** — CSRF tokens, prepared statements, input validation, output escaping, password hashing (bcrypt)

## Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.5 (strict types) |
| Database | MySQL 8+ via PDO prepared statements |
| Auth | firebase/php-jwt (access + refresh tokens) |
| Frontend | Bootstrap 5, Vanilla JS, Bootstrap Icons |
| Architecture | MVC, Service Layer, PSR-4 autoloading, PSR-12 coding style |

## Project Structure

```
task-manager/
├── app/
│   ├── Controllers/        # Web (Auth, Task, Dashboard, Profile, Comment)
│   │   └── Api/            # API controllers + JSON Resource classes (incl. Comment)
│   ├── Core/               # Router, Database, Controller, Model, Request, Response, Session, Validator, Logger
│   ├── Helpers/            # Global helper functions (escape, old, csrf, asset, url, config)
│   ├── Middleware/         # Auth, Guest, CSRF, RateLimit, Jwt (API)
│   ├── Models/             # User, Task, TaskComment, RefreshToken, LoginAttempt, ActivityLog
│   ├── Services/           # JwtService, AuthService, TaskService, CommentService, ValidationService, CsrfService
│   └── Views/              # Bootstrap 5 templates (layouts, partials, auth, tasks, dashboard, profile, errors)
├── config/                 # app.php, database.php, jwt.php
├── database/
│   ├── migrations/         # 6 table migration files
│   └── seeders/            # DatabaseSeeder.php (sample user + 10 tasks)
├── docs/                   # openapi.yaml
├── public/                 # Web root (index.php, css/, js/, uploads/, docs/)
├── routes/                 # web.php, api.php
├── storage/logs/           # Daily log files
├── tests/                  # PHPUnit unit tests
├── .env.example
├── docker-compose.yml
├── Dockerfile
├── nginx.conf
└── phpunit.xml
```

## Installation

### Prerequisites

- PHP 8.5+
- MySQL 8+
- Composer

### Setup Steps

```bash
# 1. Install PHP dependencies
composer install

# 2. Configure environment
copy .env.example .env        # Windows
# or: cp .env.example .env    # Linux/Mac

# Edit .env with your database credentials:
#   DB_HOST=127.0.0.1
#   DB_PORT=3306
#   DB_DATABASE=taskmanager
#   DB_USERNAME=root
#   DB_PASSWORD=

# 3. Create the database
mysql -u root -p -e "CREATE DATABASE taskmanager"

# 4. Run migrations (creates all 6 tables)
php database/migrate.php

# 5. Seed sample data (optional)
php database/seeders/DatabaseSeeder.php
#   Creates user: john@example.com / password123
#   Creates 10 sample tasks

# 6. Start the development server
php -S localhost:8000 -t public

# 7. Open in browser
# http://localdev.taskmanager.com:8000
```

### Quick Start with Docker

```bash
docker-compose up -d
# App: http://localdev.taskmanager.com:8000
```

### After Setup

- Register a new account at `/register`
- Or use the seeded account: `john@example.com` / `password123`

## Environment Variables

| Variable | Description | Default |
|---|---|---|
| APP_ENV | Application environment | development |
| APP_DEBUG | Show detailed errors | true |
| APP_URL | Public application URL | http://localdev.taskmanager.com |
| DB_HOST | MySQL host | 127.0.0.1 |
| DB_PORT | MySQL port | 3306 |
| DB_DATABASE | Database name | taskmanager |
| DB_USERNAME | Database user | root |
| DB_PASSWORD | Database password | |
| JWT_SECRET | JWT signing secret (change in production!) | |
| JWT_ACCESS_TTL | Access token lifetime (seconds) | 3600 (1 hour) |
| JWT_REFRESH_TTL | Refresh token lifetime (seconds) | 604800 (7 days) |

## API Endpoints

All API routes are prefixed with `/api/v1` and use Bearer token authentication.

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | /api/v1/auth/register | No | Register new user |
| POST | /api/v1/auth/login | No | Login |
| POST | /api/v1/auth/logout | Yes | Logout |
| POST | /api/v1/auth/refresh | No | Refresh access token |
| GET | /api/v1/tasks | Yes | List tasks (with filters) |
| POST | /api/v1/tasks | Yes | Create task |
| GET | /api/v1/tasks/{id} | Yes | Get task |
| PUT | /api/v1/tasks/{id} | Yes | Update task |
| DELETE | /api/v1/tasks/{id} | Yes | Delete task |
| PATCH | /api/v1/tasks/{id}/complete | Yes | Mark task complete |
| GET | /api/v1/tasks/{id}/comments | Yes | List task comments |
| POST | /api/v1/tasks/{id}/comments | Yes | Add comment |
| PUT | /api/v1/tasks/{id}/comments/{cid} | Yes | Update comment |
| DELETE | /api/v1/tasks/{id}/comments/{cid} | Yes | Delete comment |
| GET | /api/v1/profile | Yes | Get profile |
| PUT | /api/v1/profile | Yes | Update profile |
| GET | /api/v1/dashboard/stats | Yes | Dashboard statistics |

Full API documentation: `docs/openapi.yaml` or `/docs` when running the app.

## Security Features

- **OWASP Top 10 protection**: SQL injection (prepared statements), XSS (`htmlspecialchars`), CSRF (token validation), IDOR (user-scoped queries)
- **Authentication**: JWT access tokens (1h) + refresh tokens (7d) in HttpOnly/SameSite cookies
- **Password**: bcrypt hashing via `password_hash()`
- **Rate limiting**: Login throttle (5 failed attempts = 15 min lock), API rate limiting
- **Input validation**: Server-side validation for all forms and API requests
- **Logging**: All auth events and task operations logged to `storage/logs/`

## Running Tests

```bash
composer test
```

## License

MIT
