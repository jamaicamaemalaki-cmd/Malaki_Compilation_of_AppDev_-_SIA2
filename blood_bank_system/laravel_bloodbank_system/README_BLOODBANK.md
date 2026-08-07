# BloodLink Laravel Blood Bank System

This is a separate Laravel version of the Blood Bank Management System using PHP, Laravel, Bootstrap, and MySQL through XAMPP/phpMyAdmin.

## Setup

1. Open XAMPP and start Apache and MySQL.
2. Open phpMyAdmin and create a database named `bloodbank_db`.
3. In a terminal:

```bash
cd laravel_bloodbank_system
php artisan migrate
php artisan serve --port=8001
```

4. Open `http://127.0.0.1:8001`.

## Default Admin

Open `http://127.0.0.1:8001/admin/create-default` once.

Credentials:

- Email: `admin@bloodlink.test`
- Password: `admin123`

## Roles

- Admin: dashboard, blood request approvals, reports, low stock alerts.
- Donor: declaration, donation appointment request, donation history/status, blood availability.
- Medical Facility: blood requests, donor request scheduling with date and time range, facility inventory posting.

## Database

The `.env` is configured for local XAMPP:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bloodbank_db
DB_USERNAME=root
DB_PASSWORD=
```
