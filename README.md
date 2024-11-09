# PHP Web App

A simple PHP-based Web App that runs on `PHP 8.3` and uses `MySQL 8` as its database. The app serves as an example of how to structure a basic PHP project with a database connection.

## Prerequisites

Before running this application, make sure the following installed:

- **PHP 8.3** (https://www.php.net/downloads.php)
- **MySQL 8** (https://dev.mysql.com/downloads/mysql/)
- **Composer 2** (https://getcomposer.org/download/)

## Setup Instructions

Follow these steps to set up and run the application:

### 1. Clone the Repository

```bash
git clone <repository_url>
cd <repository_folder>
```

### 2. Install Dependencies

This project uses Composer for dependency management. Run the following command to install all required packages:

```bash
composer install --no-scripts
```

### 3. Configure the Database

Create a MySQL database and user for the application. Then, edit the `database.config.php` file with the appropriate connection details:

```php
// database.config.php
<?php
return [
    'host' => 'localhost',
    'port' => '3306',
    'db_name' => 'db_name',
    'username' => 'db_username',
    'password' => 'db_password',
];
```

**Creating the Tables**
   ```sql
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('Admin', 'User') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
   ```

After running this scripts, the database will have the `users` table(s) set up with the required fields and relationships.

### 4. Start the Development Server

Run the application using PHP's built-in server. In the terminal, execute:

```bash
php -S localhost:8000
```

This command will start the application at `http://localhost:8000/users`.

### 5. Access the Application

Once the server is running, open the web browser and go to:

`http://localhost:8000/users`

This will display the user listing page.

**Login Information**

To access all features, use the following login credentials:

Username: admin

Password: admin123

>Note: Update these credentials as needed in the database for security.

## Usage

Once the app is running, you can view, add, and manage users by interacting with the interface at `localhost:8000/users`.

## Troubleshooting

- **Database Connection Errors**: Ensure your MySQL server is running and `database.config.php` has the correct credentials.
- **Port Conflicts**: If `localhost:8000` is already in use, specify a different port in the `php -S` command, like `php -S localhost:8080`.

## Running Unit Tests

```sh
# check phpunit version
./vendor/bin/phpunit --version

# run all tests in the directory
./vendor/bin/phpunit tests/
```
## How set up the database for this PHP application

To set up the database for this PHP application, used PHP's PDO (PHP Data Objects) and MySQL, including three main tables: `users`.

### Database Overview

The database consists of three tables:
1. **Users**: Stores information about users who interact with the system, including their roles.

### Table Definitions

1. **Users Table**
    - **Purpose**: Stores user accounts, including both admin and regular users.
    - **Structure**:
      ```sql
      CREATE TABLE users (
          id INT AUTO_INCREMENT PRIMARY KEY,
          username VARCHAR(50) UNIQUE NOT NULL,
          password VARCHAR(255) NOT NULL,
          role ENUM('Admin', 'User') NOT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );
      ```
    - **Fields**:
      - `id`: Primary key, auto-incrementing integer.
      - `username`: Unique username for each user.
      - `password`: Hashed password for security.
      - `role`: User role, restricted to either `Admin` or `User`.
      - `created_at`: Timestamp indicating when the user account was created, with a default value of the current timestamp.

### Setting Up the Database with PDO

To connect this database in PHP, created a [Database.php](config/Database.php) class in **Singleton Pattern** that connects to MySQL using PDO.
