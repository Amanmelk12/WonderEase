# WonderEase Travel Agency Management System

A comprehensive travel agency management system built with PHP and MySQL.

## Project Structure

```text
wonderease/
├── actions/                 # Action handlers for various operations
├── admin/                  # Admin panel files
│   ├── add_package.php
│   ├── auth_check.php
│   ├── bookings.php
│   ├── create_first_admin.php
│   ├── dashboard.php
│   ├── login.php
│   ├── newsletter.php
│   ├── packages.php
│   ├── register_admin.php
│   ├── support.php
│   ├── update_booking.php
│   ├── users.php
│   └── view_booking.php
├── assets/                 # Static assets
│   ├── css/
│   ├── js/
│   └── images/
├── auth/                   # Authentication related files
├── components/            # Reusable components
├── config/               # Configuration files
├── database/            # Database related files
│   └── schema.sql
├── includes/            # Common includes
├── uploads/            # File upload directory
├── user/               # User panel files
│   ├── actions/
│   ├── auth_check.php
│   ├── book_package.php
│   ├── bookings.php
│   ├── cancel_booking.php
│   ├── dashboard.php
│   ├── get_responses.php
│   ├── packages.php
│   ├── payment.php
│   ├── profile.php
│   ├── support.php
│   └── view_booking.php
├── check_db.php        # Database connection checker
├── index.php          # Main entry point
├── package_details.php # Package details page
└── temp_bookings_schema.sql
```

## Features

### 1. User Authentication
- Registration and login system
- Role-based access (user, admin)
- Secure password handling
- Admin registration system

### 2. Customer Dashboard
- Personal information management
- Booking history and management
- Package browsing and booking
- Payment processing
- Booking cancellation
- Support ticket system

### 3. Booking System
- Package browsing and details
- Dynamic pricing
- Booking management
- Payment processing
- Booking cancellation
- Email confirmations

### 4. Admin Dashboard
- Package management (add, edit, delete)
- User management
- Booking oversight and updates
- Support ticket handling
- Newsletter management
- Booking status updates

### 5. Customer Support
- Contact form
- Ticket system
- Response management
- Real-time updates

### 6. Payment System
- Secure payment processing
- Payment status tracking
- Booking confirmation

## Setup Instructions

1. Clone the repository to your local machine
2. Import the database schema from `database/schema.sql`
3. Configure database connection in `config/database.php`
4. Start your local server (e.g., XAMPP)
5. Access the application through your web browser
6. Create first admin account using `admin/create_first_admin.php`

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

## Security Features

- Password hashing using PHP's `password_hash()`
- Session management
- SQL injection prevention
- XSS protection
- CSRF protection
- Secure file upload handling

## Database Schema

The database includes the following main tables:
- `users`
- `packages`
- `bookings`
- `payments`
- `support_messages`
- `notifications`

Detailed schema can be found in `database/schema.sql`

## License

MIT License
