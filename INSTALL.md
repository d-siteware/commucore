# Installation Guide

This guide describes how to install CommuCore on a local or production system.

---

## Requirements

CommuCore is built on Laravel and requires the following environment:

- PHP 8.3 or higher
- Composer
- Node.js (18+ recommended)
- NPM or PNPM
- MySQL / MariaDB or PostgreSQL
- Web server (Nginx or Apache)
- Git

---

## Clone the Repository

```bash
git clone https://github.com/commu-core/commucore.git
cd commucore
```

---

## Install PHP Dependencies

```bash
composer install
```

---

## Install Frontend Dependencies

```bash
npm install
```

---

## Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```


Configure your database credentials in `.env`.

---

## Database Setup

Run migrations:

```bash
php artisan migrate
```

---

## Storage & Permissions

Make sure the following directories are writable:

storage/
bootstrap/cache/

Create symbolic link for public storage:

```bash
php artisan storage:link
```

---

## Build Assets

For development:

```bash
npm run build
```

---

## Run the Application

```bash
php artisan serve
```

Application will be available at:

http://localhost:8000

---

## Production Notes

Recommended:

- Nginx
- PHP-FPM
- Supervisor (queues)
- Cron for scheduler

Scheduler cron:

```bash
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```          

---

## Support

Professional installation and customization:

https://commu-core.com
