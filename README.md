# Vehicle Rental Management System (VRMS)

A full-stack Vehicle Rental Management System developed using Laravel, MySQL, HTML, CSS and JavaScript.

## Features

* User registration and login
* Customer and Admin roles
* Vehicle listing and vehicle details
* Vehicle booking
* Booking management
* Admin vehicle management
* Admin customer management
* Admin booking approval
* Payment integration
* Contact/message management
* User profile management
* REST API using Laravel
* Authentication using Laravel Sanctum
* AI-assisted features
* Responsive frontend

## Technology Stack

### Backend

* PHP
* Laravel
* Laravel Sanctum
* MySQL

### Frontend

* HTML5
* CSS3
* JavaScript

### Tools

* VS Code
* Git
* GitHub
* Postman / Thunder Client

## Project Structure

```text
vrms-backend/
├── app/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
├── artisan
├── composer.json
└── README.md
```

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/vehicle-rental-management-system.git
```

### 2. Enter the project

```bash
cd vehicle-rental-management-system
```

### 3. Install PHP dependencies

```bash
composer install
```

### 4. Create environment file

```bash
copy .env.example .env
```

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Configure MySQL

Update the `.env` file with your local database settings.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vrms
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Run migrations

```bash
php artisan migrate
```

### 8. Start Laravel

```bash
php artisan serve
```

The application will be available at:

```text
http://127.0.0.1:8000
```

## Author

Pratap Chandra Paul


