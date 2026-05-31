<h1 align="center">Laravel Email Sandbox</h1>

<p align="center">
  A beautiful, self-hosted email testing driver &amp; dashboard for Laravel — ideal for staging environments.
</p>

---

Laravel Email Sandbox allows you to easily intercept, capture, and preview outgoing emails inside your application without sending real emails to your users. It comes with a stunning, highly responsive dashboard deeply inspired by Mailpit—built natively for Laravel with zero NPM dependencies.

Instead of paying for a hosted inbox (like Mailtrap) or installing extra software (like Mailpit) on your staging server, this package captures every outgoing email straight into your own database and renders it in a built-in dashboard.

## 📋 Requirements

- PHP `8.1` or higher
- Laravel `10.x`, `11.x`, or `12.x`

## 📑 Table of Contents

- [Key Features](#-key-features)
- [Installation](#-installation)
- [Usage](#-usage)
- [Access Control & Security](#-access-control--security)
- [Configuration](#-configuration)
- [Developed By](#-developed-by)
- [License & Open Source](#-license--open-source)

## ✨ Key Features

- **Mailpit-Inspired Interface:** Beautiful, responsive UI built with Tailwind CSS & Alpine.js (via CDN, no build step required).
- **Comprehensive Email Previews:** Preview emails in **HTML**, **Text**, and **HTML Source** formats.
- **Responsive Previews:** Interactive device toggles (Desktop, Tablet, Mobile) to test your email's responsiveness instantly.
- **Raw `.eml` Snapshotting:** Captures and stores the exact raw network string (Multipart MIME) for low-level debugging.
- **Advanced Filtering:** Instantly search your inbox by Subject, Body, Sender, Recipient, or Date Ranges.
- **Detailed Headers:** View completely parsed `Message-ID`, `Date`, `Mime-Version`, and all exact headers just like they were sent.
- **Attachment Support:** Safely capture, list, and download all email attachments natively.
- **Mass Deletion:** Keep your database clean with "Delete All" functionality.
- **Secure by Default:** Uses Laravel auth & Gates to ensure only authorized admins can access the dashboard outside of `local`.

---

## 🚀 Installation

Require the package via Composer:

```bash
composer require rnsharma93/laravel-email-sandbox
```

Publish the configuration file (optional — sensible defaults are used otherwise):

```bash
php artisan vendor:publish --tag="email-sandbox-config"
```

The package migrations are loaded automatically. Run them to create the `email_messages` table:

```bash
php artisan migrate
```

## 📨 Usage

To start capturing outgoing emails, open your `.env` file and change your mail transport driver to `email-sandbox`:

```env
MAIL_MAILER=email-sandbox
```

Now, any email sent by Laravel will be intercepted and stored in your database & local storage instead of actually being delivered.

You can view all captured emails by navigating to:
👉 **`http://your-app.test/email-sandbox`**

> [!WARNING]
> This driver **never delivers real email**. Use it on `local` and `staging` only. If you set `MAIL_MAILER=email-sandbox` in production, your real outgoing emails will be silently captured instead of sent to your users.

### Capturing only some emails

If you'd rather keep your default mailer untouched and only route specific messages to the sandbox, send them through the `email-sandbox` mailer explicitly:

```php
Mail::mailer('email-sandbox')->to($user)->send(new OrderShipped($order));
```

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

## 🧰 Configuration

After publishing the config file, you can adjust the following options in `config/email-sandbox.php`:

```php
// config/email-sandbox.php
return [
    // The URL prefix where the dashboard is served, e.g. /email-sandbox
    'route_prefix' => 'email-sandbox',

    // Where raw .eml snapshots and attachments are stored on disk
    'storage_path' => storage_path('app/email-sandbox'),
];
```

| Option         | Default                            | Description                                                    |
| -------------- | ---------------------------------- | -------------------------------------------------------------- |
| `route_prefix` | `email-sandbox`                    | URL prefix for the dashboard routes.                           |
| `storage_path` | `storage/app/email-sandbox`        | Disk location for raw `.eml` snapshots and email attachments.  |

## 👨‍💻 Developed By

**Ram Sharma**

- GitHub: [@rnsharma93](https://github.com/rnsharma93)
- Email: rns6393@gmail.com

If you find this package helpful in your day-to-day development and testing workflow, please consider starring the repository on GitHub!

## 📜 License & Open Source

The Laravel Email Sandbox package is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

You are completely free to use, modify, and distribute this package in both personal and commercial projects. Contributions, issues, and feature requests are always welcome! Feel free to check the [issues page](https://github.com/rnsharma93/laravel-email-sandbox/issues) if you want to contribute.
