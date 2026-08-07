# BloodLink Node.js Blood Bank System

Fresh Node.js/Express port of the Laravel Blood Bank Management System.

## Stack

- Node.js + Express
- EJS server-rendered views
- Bootstrap CSS
- MySQL hosted on Aiven
- Session authentication stored in MySQL

## Setup

1. Install dependencies:

```bash
npm install
```

2. Open MySQL Workbench and connect to Aiven:

- Host: `mysql-bd9539d-nodejsproject.i.aivencloud.com`
- Port: `21463`
- User: `avnadmin`
- Database: `BloodBank_db`

3. Import and run:

```text
database/schema.sql
```

4. Configure `.env`. The generated local `.env` contains the provided Aiven credentials. Keep it private. Use `.env.example` for sharing.

5. Start the app:

```bash
npm run dev
```

or:

```bash
npm start
```

6. Open `http://localhost:3000`.

## Default Accounts

Open this once after importing the schema:

```text
http://localhost:3000/admin/create-default
```

Admin:

- Email: `admin@bloodlink.test`
- Password: `admin123`

Facility accounts:

- `hch@bloodlink.test`
- `ztlmh@bloodlink.test`
- `hrhu@bloodlink.test`
- `prc@bloodlink.test`
- Password: `facility123`

## Useful Commands

```bash
npm run build
npm run db:test
npm start
```
