# LocalSkill

LocalSkill is a Laravel application for finding student services and managing service listings.

## Local Installation Guide

These instructions use **Windows PowerShell** and a **local MySQL database**. Run commands one at a time. The application files shared with this guide do not establish exact PHP or Node.js version requirements; use the versions required by `composer.json`, `composer.lock`, and `package.json` in your checkout.

### 1. Install the prerequisites

Install the following tools and make sure they are available in your terminal:

- [Git](https://git-scm.com/downloads)
- [PHP](https://www.php.net/downloads.php), compatible with the project's Composer requirements
- [Composer](https://getcomposer.org/download/)
- [Node.js and npm](https://nodejs.org/), compatible with the project's frontend dependencies
- MySQL, either installed separately or supplied by a local environment such as Laragon or XAMPP

Verify the installations:

```powershell
git --version
php -v
composer --version
node -v
npm -v
```

If a command is not recognized, install the tool or correct its PATH entry, then reopen PowerShell.

### 2. Clone the main branch

Open PowerShell in the parent folder where you want to store the project:

```powershell
git clone --branch main https://github.com/MBarikhZidane/LocalSkill-Gayatama.git
cd LocalSkill-Gayatama
git branch --show-current
```

The last command should print `main`. Private repositories require GitHub access. Use the repository URL above, not a browser URL ending in `/tree/main`.

Check the project files:

```powershell
dir
```

Run the remaining commands from the directory containing `artisan`, `composer.json`, and `package.json`. If these files are inside a subfolder, enter that folder first.

### 3. Enable the required PHP extensions

Find the configuration used by command-line PHP:

```powershell
php --ini
```

Open the file shown under **Loaded Configuration File**. For example, if it is `C:\php_all\php.ini`:

```powershell
notepad C:\php_all\php.ini
```

For this setup, ensure the following extensions are enabled. Remove a leading semicolon from an existing entry instead of adding duplicate entries:

```ini
extension=fileinfo
extension=zip
extension=pdo_mysql
```

These are the extensions relevant to the known installation errors and the MySQL setup, not an exhaustive list of Laravel's requirements. Enable any additional extensions requested by Composer.

Save the file and verify:

```powershell
php -m | Select-String 'fileinfo|zip|pdo_mysql'
```

Each new PHP CLI command reads the configuration again. If PHP reports an extension loading error, check `extension_dir` and whether the corresponding DLL exists in the PHP installation's `ext` directory.

### 4. Install PHP dependencies

```powershell
composer install
```

This installs the versions recorded in `composer.lock`. Do not use `composer update` or ignore platform requirements to work around missing PHP extensions.

If installation fails, fix the reported problem and rerun `composer install` before continuing.

### 5. Create the local environment file

Create `.env` only if it does not already exist:

```powershell
if (!(Test-Path .env)) { Copy-Item .env.example .env }
```

If `.env.example` is missing, obtain the project's environment template from the maintainer. Do not copy production credentials into a local installation.

For a new local installation, generate the application key:

```powershell
php artisan key:generate
```

Generate this key once during setup; do not regenerate it every time you start the application.

### 6. Configure a local MySQL database

Start MySQL and create an empty database named `localskill` using phpMyAdmin, a database client, or the following SQL in a MySQL session:

```sql
CREATE DATABASE localskill CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Open `.env`:

```powershell
notepad .env
```

Update the existing entries to match your local setup:

```dotenv
APP_NAME=LocalSkill
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=localskill
DB_USERNAME=root
DB_PASSWORD=
```

The example assumes a local `root` account with no password. Replace the username, password, and port with your own values. Keep the other project-specific settings from `.env.example`. `APP_DEBUG=true` is for local development only.

Clear cached configuration and create the tables:

```powershell
php artisan config:clear
php artisan migrate
```

Migrations create the schema; they do not copy production data or uploaded files. If the application requires initial categories or other reference data, review `database/seeders` and the maintainer's instructions. Only if the project's seeders are intended for local setup, run:

```powershell
php artisan db:seed
```

This guide does not assume that seeders or default login credentials exist.

### 7. Install frontend dependencies

If the project includes `package-lock.json`, use:

```powershell
npm ci
```

If it does not include an npm lockfile and npm is the project's package manager, use:

```powershell
npm install
```

If the project uses another package manager's lockfile, follow that package manager instead. If `npm ci` reports a mismatch between the manifest and lockfile, resolve that mismatch with the maintainer.

### 8. Link public storage

```powershell
php artisan storage:link
```

This exposes files stored in `storage/app/public` through `public/storage`. A message saying the link already exists normally means this step has already been completed. On Windows, a symbolic-link permission error may require Developer Mode or an elevated terminal.

### 9. Start Laravel and the frontend

In the first terminal, from the project directory:

```powershell
php artisan serve
```

Open a second PowerShell terminal, enter the same project directory, and run:

```powershell
npm run dev
```

Keep both terminals open. These frontend commands assume a `dev` script is defined in `package.json`; run `npm run` to inspect the available scripts if it is missing.

Open **http://127.0.0.1:8000** in your browser. Use the Laravel URL, not the Vite development-server URL, to access the application.

If you only need compiled assets and the project provides a `build` script, you can run `npm run build` instead of keeping Vite running. Rebuild after changing frontend assets.

Some features may require additional services, such as mail delivery, a queue worker, or broadcasting. Configure those according to the project's environment template. If the application uses an asynchronous queue for a feature, run `php artisan queue:work` in a separate terminal for that feature.

### 10. Start the application on later visits

Start your local MySQL service, then run these commands in separate terminals from the project directory:

```powershell
php artisan serve
```

```powershell
npm run dev
```

You do not need to clone again, recreate `.env`, regenerate the key, or reinstall unchanged dependencies. Stop a development server with **Ctrl + C** in its terminal.

## Troubleshooting

| Error | What to check |
| --- | --- |
| `ext-fileinfo` is missing | Enable `extension=fileinfo` in the CLI PHP configuration, then rerun Composer. |
| ZIP extension and unzip/7z commands are missing | Enable `extension=zip`, verify it with `php -m`, then rerun Composer. |
| `Could not open input file: artisan` | Enter the project directory containing the `artisan` file. |
| `vendor/autoload.php` is missing | Complete `composer install` successfully. |
| `No application encryption key has been specified` | Ensure `.env` exists, generate a key for the new local installation, then run `php artisan config:clear`. |
| `could not find driver` with MySQL | Enable `pdo_mysql` in the PHP configuration used by the terminal. |
| Database connection refused | Start MySQL and verify the host and port in `.env`. |
| Access denied or unknown database | Check the local credentials and create the configured database. Run `php artisan config:clear` after editing `.env`. |
| `Vite manifest not found` | Install frontend dependencies and run `npm run dev`, or run the project's build script. |
| `npm.ps1 cannot be loaded` | Try `npm.cmd` in place of `npm`, such as `npm.cmd ci` and `npm.cmd run dev`. |
| Port 8000 is occupied | Run `php artisan serve --port=8001` and open `http://127.0.0.1:8001`. Adjust `APP_URL` if needed. |
| A page returns 404 | Inspect `php artisan route:list`, check route order and parameter constraints, and clear stale routes with `php artisan route:clear`. |

For application errors, inspect the terminal output and files under `storage/logs`. Remove credentials and other sensitive values before sharing logs.

## Configuration and Credentials

- Keep `.env`, `vendor`, and `node_modules` out of Git; verify the project's `.gitignore`.
- Keep dependency lockfiles in version control so installations use consistent versions.
- Use local database credentials and local test data.
- Cloning and running this project does not update the hosted website.

## Documentation

- [Laravel documentation](https://laravel.com/docs) — select the version matching `composer.json`.
- [Composer: installing dependencies](https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies)
- [PHP configuration](https://www.php.net/manual/en/configuration.file.php)

