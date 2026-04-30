
This is a group six web based Village Bank Management System project given as final project for Web Development module.

It allows administrators to track members, savings, loans, and transactions in a simple and organized way.

---

## Features

- Member registration and management
- User authentication (login/logout, password resets/password changes)
- Savings tracking
- Loan management
- Transaction history
- Dashboard with summary statistics

---

## Technologies Used

- PHP (Core PHP)
- MySQL
- HTML
- CSS
- JavaScript

---

## Project Structure

```text
/project-root
│── /auth
│── /pages
│── layout.php
│── index.php
│── /utils
│── /includes
│── /statics
```

---

## Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/NigelNk/village-bank-management-system.git
```

### 2. Move the Project to Your Server Directory

- XAMPP → `htdocs/`
- WAMP → `www/`

### 3. Create Database

Open phpMyAdmin and create:

```sql
village_bank
```

### 4. Configure Database Connection

Inside the `utils` folder, create a file named:

```php
config.php
```

This file is not included in the repository because it contains sensitive information such as:

- Database username
- Password
- Database name
- Server credentials

Use the following code inside `config.php`:

```php
<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // replace this with your servername otherwise leave it like that for wamp/xamp
define('DB_PASS', ''); // replace '' with your actual db password otherwise leave it like that especially for xamp/wamp
define('DB_NAME', 'village_bank');

function get_server_db()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

    if ($conn->connect_error) {
        die("Server connection failed: " . $conn->connect_error);
    }

    return $conn;
}

function get_db()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>
```

### 5. Run the Project

```text
http://localhost/village-bank-management-system
```

---

## Security Note

Do not upload your real `config.php` file to GitHub.

Add this to `.gitignore`:

```gitignore
/utils/config.php
```
