# Woof-woof PetShop E-commerce Platform

## Overview

Woof-woof PetShop is a comprehensive e-commerce platform designed specifically for pet products and supplies. The application is built using PHP with a MVC (Model-View-Controller) architecture and MySQL database. The platform provides a seamless shopping experience for customers while offering robust administrative features for store management.

## Features

### Customer-Facing Features

- User registration and authentication system with real-time validation
- Google login integration for simplified account creation
- Comprehensive product catalog with categories and search functionality
- Responsive design for optimal viewing on all devices
- Shopping cart functionality with session management
- Secure checkout process
- Order tracking and history for registered users
- User profile management with customizable avatars
- Password strength validation and security measures

### Administrative Features

- Secure admin dashboard with analytics
- Product management (add, edit, delete products)
- Category management
- Order management with status updates
- User account management
- Store location management
- Real-time monitoring of online users

## Technical Implementation

The application implements several modern web development techniques:

- MVC architectural pattern for clean code separation
- AJAX for asynchronous data loading and form validation
- Responsive design using Bootstrap 5
- Client-side validation with JavaScript
- Server-side validation with PHP
- RESTful API endpoints for AJAX requests
- Session management for user authentication and cart persistence
- Password hashing for security
- Input sanitization to prevent SQL injection and XSS attacks

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP, WAMP, or similar package (for local development)
- Modern web browser (Chrome, Firefox, Edge, Safari)

## Installation

1. Clone the repository to your local machine
2. Import the database schema:
   - Create a new MySQL database named `chabong_shop`
   - Import the SQL file `chabong_shop.sql`
3. Configure database connection:
   - Open `database.php`
   - Update the database credentials to match your environment
4. Configure site URL:
   - Open `config.php`
   - Update the `SITE_URL` constant to match your environment
5. For Google login functionality (optional):
   - Create a project in Google Developer Console
   - Generate OAuth 2.0 credentials
   - Update `google_config.php` with your credentials

## Running the Application

### Local Development with XAMPP:

- Start Apache and MySQL services from the XAMPP Control Panel
- Place the project folder in the htdocs directory
- Access the application via http://localhost/petshop/

## Access Credentials

### Admin Access

- URL: http://localhost/petshop/admin/
- Username: `admin`
- Password: `admin123`

The admin dashboard provides access to:

- Sales analytics and statistics
- Product inventory management
- Order processing and fulfillment
- User account management
- Category management
- Store location settings

### Customer/User Access

- URL: http://localhost/petshop/user/login

Demo User Accounts:

1. Regular User:

   - Username: `user1`
   - Password: `abcd1234@`
   - This account has completed orders and profile information

2. Google-linked User:
   - Use your Google account to login

### Testing Registration

You can also test the user registration system by creating a new account:

- Navigate to http://localhost/petshop/user/signup
- The registration form includes real-time validation for username and email availability

## Dependencies and Setup

### Composer Dependencies

This project uses Composer to manage PHP dependencies. If you encounter errors related to missing vendor files:

1. Install Composer:

   - Download from [getcomposer.org](https://getcomposer.org/download/)
   - Follow installation instructions for your operating system

2. Install dependencies:

   ```bash
   cd /path/to/petshop
   composer require google/apiclient:"^2.0"
   ```

3. If you encounter memory limit issues:

   ```bash
   php -d memory_limit=-1 /path/to/composer.phar install
   ```

4. Update dependencies:
   ```bash
   composer update
   ```

The vendor directory contains essential libraries and should be properly initialized before running the application.

## Environment Configuration

This project uses environment variables for configuration. To set up:

1. Copy `.env.example` to `.env` and fill in your credentials
2. Copy `config/database.example.php` to `config/database.php` (if not using environment variables)
3. Copy `config/google_config.example.php` to `config/google_config.php` (if not using environment variables)

### Google OAuth Setup

1. Create a project in Google Developer Console
2. Set up OAuth credentials for Web Application
3. Add authorized redirect URI: `http://localhost/petshop/user/google-callback` 
   (adjust based on your actual domain)
4. Add credentials to your `.env` file
