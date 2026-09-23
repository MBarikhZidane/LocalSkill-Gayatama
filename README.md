# LocalSkill

LocalSkill is a web application that connects people looking for services with student service providers. Students can showcase their skills, build portfolios, and publish service listings, while customers can explore services, place orders, and communicate with providers.

## Features

- **Service discovery:** Browse service listings and view service details.
- **Provider profiles:** Explore providers’ skills, portfolios, and reviews.
- **Service management:** Create, update, and manage service listings.
- **Skills and portfolios:** Showcase expertise and examples of previous work.
- **Order management:** Place service orders, view buying and selling activity, and track order status.
- **Messaging:** Communicate through conversations within the application.
- **Ratings and reviews:** Share feedback on services.
- **Notifications:** Receive updates about order activity.
- **Account management:** Register, sign in, and update profile information, with Google sign-in support.
- **Administration:** Manage users, services, categories, skills, portfolios, and orders.

## User Roles

- **Customers** discover services, place orders, and communicate with providers.
- **Providers** showcase their skills, publish services, and manage incoming orders.
- **Administrators** oversee platform records through the administration dashboard.

## Technology Stack

- **Backend:** PHP and Laravel
- **Frontend:** Laravel Blade, Tailwind CSS, and Alpine.js
- **Asset tooling:** Vite
- **Database:** MySQL for the documented local setup
- **Authentication:** Laravel Breeze and Laravel Socialite
- **Dependency management:** Composer and npm

## Getting Started

See the [Installation Guide](INSTALLATION.md) for prerequisites, local configuration, database setup, startup commands, and troubleshooting.

Google sign-in requires its own OAuth configuration.

## Project Structure

- `app/` — Application logic, controllers, models, and notifications.
- `bootstrap/` — Application initialization.
- `config/` — Application configuration.
- `database/` — Migrations, factories, and seeders.
- `public/` — Web entry point and public assets.
- `resources/` — Blade templates, styles, and JavaScript.
- `routes/` — Application route definitions.
- `storage/` — Generated files, logs, and application storage.
- `tests/` — Automated tests.

## Project Purpose

LocalSkill aims to help students turn their skills into service opportunities and make their work easier for potential customers to discover.
