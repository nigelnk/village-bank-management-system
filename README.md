# Village Bank Management System

## Overview

This project is a web based Village Bank Management System designed to help small community savings groups manage their financial activities efficiently.

It allows administrators to track members, savings, loans, and transactions in a simple and organized way.

---

## Features

* Member registration and management
* User authentication (login/logout, password resets/password changes)
* Savings tracking
* Loan management
* Transaction history
* Dashboard with summary statistics

---

## Technologies Used

* PHP (Core PHP)
* MySQL (Database)
* HTML, CSS
* JavaScrip

---

## Project Structure

```
/project-root
│── /auth
│── /pages
│── layout.php
│── index.php
│── /utils
│── includes
│── /includes
│── /statics
```

---

## Installation & Setup

1. Clone the repository:

```
git clone https://github.com/your-username/village-bank-management-system.git
```

2. Move the project to your server directory:

* For XAMPP: `htdocs/`
* For WAMP: `www/`

3. Create a database:

* Open phpMyAdmin
* Create a database (e.g. `village_bank`)

4. Import the SQL file:

* Import `database.sql` into your database

5. Configure database connection:

* Open `config.php`
* Update:

```
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'village_bank';
```

6. Run the project:

* Open browser
* Go to:

```
http://localhost/village-bank-system
```

