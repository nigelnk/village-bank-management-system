
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
```
Do not upload your real `config.php` file to GitHub.
```
## Composer And FPDF
## Why we use Composer (and FPDF)

This project uses **FPDF** to generate PDF documents. Instead of manually downloading the FPDF files and "copy-pasting" them into our project, we use **Composer**.

### Why use Composer for a single library?
Even though FPDF is our only external tool, Composer is used because:
*   **Easy Updates:** If FPDF releases a security fix, we can update it with one command (`composer update`) rather than re-downloading files manually.
*   **Autoloading:** It handles the `require` paths for us. We just write `require 'vendor/autoload.php'` and FPDF is ready to use.
*   **Clean Repository:** It allows us to keep the actual FPDF source code out of our Git history, keeping our project "light."

### How to get started
Because we use Composer, the FPDF library files are **not** in this repository. To get them:

1.  **Install the library:** Run this in your terminal:
    ```bash
    composer install
    composer require setasign/fpdf
    ```
    * Then
    ````
    Download fpdf files: https://fpdf.org/en/dl.php?v=186&f=zip
    then extract the zipped file put it in the directory inside the root project directory

    Include this in the code that needs fpdf
    require 'vendor/autoload.php';
    $pdf = new FPDF();
    ```
2.  **Verify:** This will create the `vendor/` folder on your machine, which contains the FPDF source code.

### Document Generation
With FPDF, this project can:
*   Generate dynamic reports and invoices.
*   Control page layouts, fonts, and colors through PHP.
*   Output PDF files directly to the browser or save them to the server.


Add this to `.gitignore`:

```gitignore
/utils/config.php
/vendor
*.pdf
```
