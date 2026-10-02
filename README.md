# G-SCHED - Guidance Scheduling Management System

A complete, full-featured web-based Guidance Scheduling Management System built with Laravel 12, PHP 8.2+, MySQL, and Bootstrap 5.

## Features

### Student Module
- User registration and authentication
- View available counseling schedules
- Book appointments with preferred date/time
- View appointment status and details
- Cancel appointments
- Request rescheduling
- Submit feedback after completed sessions
- Receive notifications (in-app)
- Profile management

### Guidance Associate Module
- Dashboard with pending requests, today's appointments, statistics
- Review and approve/reject appointment requests
- Reschedule appointments
- Cancel appointments
- Mark appointments as completed
- Send reminders to students
- Manage availability (add/edit/delete time slots)
- Calendar view with FullCalendar integration
- Appointment history
- Notifications

### Admin Module
- Complete user management (CRUD operations)
- Role-based access control (Student, Guidance Associate, Admin)
- System settings configuration
- Appointment management and monitoring
- Comprehensive reports:
  - Appointment Report
  - Student Report
  - Guidance Associate Report
  - Status Distribution Report
  - Monthly Trends Report
  - Cancellation Report
  - Feedback Report
- Export reports to CSV
- Activity logs with filtering
- System notifications

### Technical Features
- Role-based access control with middleware
- Database transactions for data integrity
- Activity logging for all important actions
- In-app notification system
- Responsive Bootstrap 5 UI (mobile-friendly)
- Google Calendar API integration ready
- Email notification support (configurable)
- Appointment reminders (24h and 1h before)
- Charts with Chart.js
- FullCalendar integration

## Requirements

- PHP 8.2+
- Composer
- MySQL 5.7+ / MariaDB 10.3+
- Node.js & NPM (for frontend assets)
- XAMPP/WAMP/LAMP (for local development)

## Installation

### 1. Clone the repository
```bash
cd g-sched
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Install Node.js dependencies
```bash
npm install
```

### 4. Build frontend assets
```bash
npm run build
```

### 5. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

### 6. Configure database
Edit `.env` and set your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=g_sched
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Create database
```bash
# Using MySQL command line
mysql -u root -p
CREATE DATABASE g_sched CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 8. Run migrations and seeders
```bash
php artisan migrate --seed
```

### 9. Start the development server
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Demo Credentials

After running the seeders, you can log in with:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@g-sched.test | password |
| Guidance Associate | guidance@g-sched.test | password |
| Student | student@g-sched.test | password |

## Google Calendar Integration (Optional)

To enable Google Calendar synchronization:

1. Create a Google Cloud Project
2. Enable Google Calendar API
3. Create OAuth 2.0 credentials
4. Add to `.env`:
```env
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=http://localhost/google-calendar/callback
```

## Project Structure

```
g-sched/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # All controllers
│   │   ├── Middleware/      # Role middleware
│   │   └── Requests/        # Form requests
│   ├── Models/              # Eloquent models
│   └── Services/            # Business logic services
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── resources/
│   ├── views/
│   │   ├── layouts/         # Layout files
│   │   ├── auth/            # Authentication views
│   │   ├── student/         # Student module views
│   │   ├── guidance/        # Guidance associate views
│   │   ├── admin/           # Admin module views
│   │   └── components/      # Reusable components
│   ├── css/                 # Stylesheets
│   └── js/                  # JavaScript files
├── routes/
│   ├── web.php              # Web routes
│   └── api.php              # API routes
└── public/
    ├── css/
    ├── js/
    └── images/
```

## Key Database Tables

- `roles` - User roles (student, guidance_associate, admin)
- `users` - All system users
- `appointment_statuses` - Appointment status definitions
- `appointments` - Appointment records
- `availability` - Counselor availability slots
- `reschedule_requests` - Reschedule requests
- `feedback` - Student feedback
- `notifications` - In-app notifications
- `activity_logs` - System activity logs
- `system_settings` - Configurable system settings

## Security Features

- Password hashing with bcrypt
- CSRF protection on all forms
- SQL injection prevention via Eloquent ORM
- XSS prevention with Blade templating
- Role-based authorization middleware
- Session protection
- Input validation (server-side and client-side)

## Development

### Running tests
```bash
php artisan test
```

### Code style
```bash
./vendor/bin/pint
```

### Database refresh
```bash
php artisan migrate:fresh --seed
```

## Production Deployment

1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Generate a strong `APP_KEY`
3. Configure production database
4. Set up queue workers for background jobs:
   ```bash
   php artisan queue:work
   ```
5. Set up task scheduler for reminders:
   ```bash
   * * * * * cd /path/to/g-sched && php artisan schedule:run >> /dev/null 2>&1
   ```
6. Configure proper mail settings
7. Set up SSL/HTTPS
8. Configure file permissions

## License

This project is developed for educational/thesis purposes.

## Support

For issues or questions, please check the Laravel documentation or create an issue in the repository.