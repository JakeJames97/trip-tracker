# Trip Tracker

A small full-stack application for planning trips, built to demonstrate a Laravel API working with a Vue 3 single-page frontend and a relational database.

The domain is a simple hierarchy: a **trip** contains **destinations**, and each destination has a checklist of **tasks**. Users register, log in, and manage their own trips through full CRUD, with per-destination budgets that are converted to local currency via a live exchange-rate API.

---

## Stack

**Backend**
- Laravel (PHP 8.5), served via Laravel Sail (Docker)
- MySQL Database
- Sanctum token authentication
- Saloon for the 3rd party exchange-rate integration

**Frontend**
- Vue 3 / TypeScript
- Pinia for state management
- Vue Router
- VeeValidate + Yup for form validation
- Vite
- Axios for data fetching from the API
- Sass for styling

**Tooling**
- PHPUnit (feature + unit tests)
- Larastan / PHPStan (static analysis)
- Pint / Eslint (code style)

---
## Domain Improvements

- I would implement loading skeleton placeholders to make the website feel smoother
- Allow trip budgets to accept other currencies, rather than assuming GBP
- Support drag and drop for moving around tasks or even destinations
- Allow image uploads for galleries etc
- Trip sharing between users
- Weather forecasts for each destination
- Add a scheduled command that automatically updates trip statuses based on their dates (e.g. marking trips completed once past their end date).
---

## Technical Improvements

- Add a mapping layer at the API level so a change to an API field only needs updating in one place rather than throughout the app. This could be done via 'model' style classes.
- DTOs via spatie/laravel-data — would consolidate the request / resource / TypeScript-type duplication into a single source of truth, and can even generate the frontend types.
- Unit tests via vitest & E2E tests via cypress for the frontend to improve code stability and reliability
- Implement CI/CD pipelines
---

## Application Setup

This project runs entirely through Laravel Sail, so the only host requirement is Docker.

```bash
# 1. Clone
git clone <REPO_URL>
cd trip-tracker

# 2. Environment
cp .env.example .env

# 3. Composer Install
composer install

# 4. Start the containers
./vendor/bin/sail up -d

# 5. App key, database, seed data
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed

# 6. Frontend
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev

# 7. Running the test suite
./vendor/bin/sail artisan test
```
