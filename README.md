<p align="center">
  <h1 align="center">Laravel Email Sandbox</h1>
  <p align="center">
    A beautiful, production-ready, self-hosted email testing driver & dashboard for Laravel.
  </p>
</p>

---

Laravel Email Sandbox allows you to easily intercept, capture, and preview outgoing emails inside your application without sending real emails to your users. It comes with a stunning, highly responsive dashboard deeply inspired by Mailpit—built natively for Laravel with zero NPM dependencies.

## ✨ Key Features

- **Mailpit-Inspired Interface:** Beautiful, responsive UI built with Tailwind CSS & Alpine.js (via CDN, no build step required).
- **Comprehensive Email Previews:** Preview emails in **HTML**, **Text**, and **HTML Source** formats.
- **Responsive Previews:** Interactive device toggles (Desktop, Tablet, Mobile) to test your email's responsiveness instantly.
- **Raw `.eml` Snapshotting:** Captures and stores the exact raw network string (Multipart MIME) for low-level debugging.
- **Advanced Filtering:** Instantly search your inbox by Subject, Body, Sender, Recipient, or Date Ranges.
- **Detailed Headers:** View completely parsed `Message-ID`, `Data`, `Mime-Version`, and all exact headers just like they were sent.
- **Attachment Support:** Safely capture, list, and download all email attachments natively.
- **Mass Deletion:** Keep your database clean with "Delete All" functionality.
- **Production Secure:** Uses Laravel auth & Gates to ensure only authorized admins can access your sandbox in production.

---

## 🚀 Installation

Require the package via Composer:

```bash
composer require rnsharma93/laravel-email-sandbox
```

Publish the configuration file and migrations:

```bash
php artisan vendor:publish --tag="email-sandbox-config"
php artisan vendor:publish --tag="email-sandbox-migrations"
```

Run the database migrations to create the `email_messages` table:

```bash
php artisan migrate
```

## ⚙️ Usage

To start capturing outgoing emails, open your `.env` file and change your mail transport driver to `email-sandbox`:

```env
MAIL_MAILER=email-sandbox
```

Now, any email sent by Laravel will be intercepted and stored in your database & local storage instead of actually being delivered. 

You can view all captured emails by navigating to:
👉 **`http://your-app.test/email-sandbox`**

---

## 🔐 Access Control & Security

By default, the Email Sandbox UI is fully accessible in the `local` environment. 

However, if you deploy this to `production`, `staging`, or `testing` environments, the route is completely locked down by default. To authorize specific users to view the dashboard in non-local environments, you must define the `accessEmailSandbox` Gate in your application's `App\Providers\AppServiceProvider`:

```php
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::define('accessEmailSandbox', function ($user) {
        // Return true if the user is an admin or authorized to view intercepted emails
        return in_array($user->email, [
            'admin@yourdomain.com',
        ]);
    });
}
```

---

### File Storage (Raw `.eml` & Attachments)
By default, the package stores raw `.eml` snapshots and attachments in `storage/app/email-sandbox`. You can modify this path in the `config/email-sandbox.php` file:

```php
// config/email-sandbox.php
return [
    'storage_path' => storage_path('app/email-sandbox'),
    
    // You can also change the default route prefix
    'route_prefix' => 'email-sandbox', 
];
```

## 📜 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 👨‍💻 Author
**Ram Sharma**
