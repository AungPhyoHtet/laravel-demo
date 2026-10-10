# Laravel Demo

A small Laravel 13 app where users sign up, log in, and manage their own ideas. Admins get an extra dashboard that lists every user's ideas.

**Built with** Laravel 13, Tailwind CSS 4, daisyUI 5, Alpine.js 3, Vite 8, and Pest 4 (with the browser plugin).

## Features

- **Authentication**: register, log in, and log out. Login attempts are limited to 5 per minute per email and IP address.
- **Ideas**: create, view, edit, and delete ideas with a status (pending, in progress, completed), an optional image, links, and steps. The ideas list can be filtered by status. `IdeaPolicy` keeps each user's ideas private to them.
- **Steps**: check and uncheck an idea's steps straight from the idea page.
- **Notifications**: publishing an idea sends the owner an email and an in-app notification. The bell in the nav shows the unread count, and notifications can be marked as read.
- **Profile**: `/profile` lets users change their name, email, and password. Changing the password requires the current password, and changing the email resets email verification. Every change sends an email listing what changed, and an email change also notifies the previous address.
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
- Node.js 22.12+ and npm
- SQLite (the default database)

## Setup

```bash
composer run setup
```

This installs PHP and JS dependencies, creates `.env`, generates the app key, runs migrations, and builds the frontend assets.

Link the public storage disk so uploaded idea images can be served:

```bash
php artisan storage:link
```

To seed a demo user (`test@example.com` / `password`) with 20 ideas:

```bash
php artisan db:seed
```

## Development

```bash
composer run dev
```

This starts the server, the Vite dev server, log tailing, and a queue worker. Notification emails are queued, so they only go out while a queue worker is running. With the default `MAIL_MAILER=log`, emails are written to `storage/logs/laravel.log`.

When served with [Laravel Herd](https://herd.laravel.com), the app is also available at `http://laravel-demo.test`.

## Testing

The test suite uses [Pest](https://pestphp.com):

```bash
php artisan test --compact
```

Or call Pest directly:

```bash
vendor/bin/pest                                          # the whole suite
vendor/bin/pest tests/Feature/ProfileControllerTest.php  # one file
vendor/bin/pest --filter="updates the name and email"    # tests matching a name
vendor/bin/pest tests/Browser                            # only the browser tests
vendor/bin/pest --parallel                               # run in parallel
```

Browser tests in `tests/Browser` use [Pest's browser plugin](https://pestphp.com/docs/browser-testing) and need Playwright's browsers installed once:

```bash
npx playwright install
```

Format code with Pint before committing:

```bash
vendor/bin/pint --dirty
```

## License

Open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
