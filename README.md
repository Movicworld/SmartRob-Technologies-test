# Laravel Email Scheduler - SmartRob-Technologies

## Overview

This Laravel application allows users to authenticate, schedule emails, manage scheduled emails, and process them via queues and jobs. The system also supports email templates and automatic retrying of failed emails.

---

## Features

- User authentication using Laravel Sanctum
- Schedule emails to be sent at a later time using gmail mailing service
- Prevent duplicate scheduling within 5 minutes
- Queue and job processing for efficient email delivery
- View, edit, delete, and reschedule emails
- Email templates for reusing email content
- Cron job to retry failed emails automatically

---

## Installation & Setup

### 1️⃣ Clone the Repository

```sh
git clone https://github.com/Movicworld/SmartRob-Technologies-test.git
cd SmartRob-Technologies-test
```

### 2️⃣ Install Dependencies

```sh
composer install
```

### 3️⃣ Setup Environment Variables

Copy the `.env.example` file to `.env`:

```sh
cp .env.example .env
```

Then update the following in `.env`:

```env
APP_NAME=SmartRobTest-VictorMorakinyo
APP_ENV=local
APP_KEY=base64:YOUR_APP_KEY
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=email_scheduler
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com // Google Mail used
MAIL_PORT=587
MAIL_USERNAME=morakinyovictor1@gmail.com
MAIL_PASSWORD=iwmuxhhwecbjmqeo //password will be invalid after 7days.
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=morakinyovictor1@gmail.com
MAIL_FROM_NAME="SmartRob Test"
```

### 4️⃣ Generate Application Key

```sh
php artisan key:generate
```

### 5️⃣ Setup Database

Run migrations and seed data:

```sh
php artisan migrate --seed
```

### 6️⃣ Run the Application

```sh
php artisan serve
```

---

## Running the Queues & Jobs

Ensure you run the queue worker:

```sh
php artisan queue:work
```

---

## Running Scheduled Jobs & Failed Email Retries

Laravel's scheduler should run via a cron job:

```sh
php artisan schedule:work
```

---
## API Endpoints (Postman Collection)

Import the Postman collection from postman/email-scheduler.postman_collection.json.
```

---

## License

MIT License

---

## Author

Victor Oluwafemi Morakinyo

