# Laravel Demo

A small Laravel 13 app where users sign up, log in, and manage their own ideas. Admins get an extra dashboard that lists every user's ideas.

## Features

- **Authentication**: register, log in, and log out. Login attempts are limited to 5 per minute per email and IP address.
- **Ideas**: create, view, edit, and delete ideas. `IdeaPolicy` keeps each user's ideas private to them.
- **Admin area**: `/admin` shows the total user count and all ideas with their authors. The **Admin** nav link only appears for admins.

## Authorization

| Check | Where | Rule |
| --- | --- | --- |
| `IdeaPolicy` (`view`, `update`, `delete`) | `app/Policies/IdeaPolicy.php` | Only the idea's owner. Other users get a 403. |
| `access-admin` Gate | `app/Providers/AppServiceProvider.php` | Only users with `is_admin = true`. Other users get a **404**, so the admin area stays hidden. |

The `/admin` route uses the `can:access-admin` middleware, and the nav link is wrapped in `@can('access-admin')`.

### Making a user an admin

```bash
php artisan tinker --execute 'App\Models\User::where("email", "you@example.com")->update(["is_admin" => true]);'
```

In tests and seeders, use the factory state: `User::factory()->admin()->create()`.

## Requirements

- PHP 8.3+
- Composer
- Node.js and npm
- SQLite (the default database)

## Setup

```bash
composer run setup
```

This installs PHP and JS dependencies, creates `.env`, generates the app key, runs migrations, and builds the frontend assets.

## Development

```bash
composer run dev
```

When served with [Laravel Herd](https://herd.laravel.com), the app is also available at `http://laravel-demo.test`.

## Testing

The test suite uses [Pest](https://pestphp.com):

```bash
php artisan test --compact
```

Format code with Pint before committing:

```bash
vendor/bin/pint --dirty
```

## License

Open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
