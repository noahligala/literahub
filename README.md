# LiteraHub Platform

**Author:** Ligco Technologies  
**Project type:** Subscription-based education SaaS  
**Primary framework:** Laravel 13  
**Status:** Initial starter architecture / MVP foundation  
**Licence:** Proprietary

LiteraHub is a web-first digital literature platform designed for schools, universities, teachers, authors, publishers, and individual students. It enables institutions and learners to subscribe to protected literary resources, read approved books online, receive assignments, track reading progress, manage users, and pay for access through configurable payment providers.

The project is designed as a **modular Laravel monolith**. This keeps the first implementation affordable and maintainable while avoiding infrastructure-specific business logic. Storage, payments, queues, notifications, caching, and databases are accessed through Laravel abstractions or application contracts so providers can be changed later without rewriting the whole system.

> This repository is an implementation-ready starter. It includes the project structure, core domain models, initial migrations, service contracts, starter interfaces, Docker configuration, CI configuration, and project documentation. Authentication screens, complete Filament panels, payment-provider production credentials, the protected document reader, assessments, and several advanced modules still require implementation.

---

## 1. Product objectives

LiteraHub should provide:

- Subscription access for schools, universities, and individual students
- A protected online library for novels, plays, poetry, study guides, academic papers, lecture notes, audio, and video
- School administration for teachers, students, classes, licence allocation, and usage reporting
- Author and content-management workflows
- Reading progress, notes, highlights, bookmarks, and assignments
- M-Pesa, card, and manual institutional payment support
- Invoices, receipts, renewals, expiry rules, and payment callbacks
- Role-based access control and auditable content access
- A responsive, installable web application that works across desktops, tablets, and phones

---

## 2. Intended users

| Role | Main responsibilities |
|---|---|
| Platform administrator | Manages schools, users, plans, content, payments, reports, and platform settings |
| Author/content manager | Uploads, categorises, updates, and reviews literary resources |
| School administrator | Manages a school subscription, teachers, students, classes, and licence allocation |
| Teacher/lecturer | Assigns resources, creates assessments, and reviews learner progress |
| Student | Reads resources, saves notes, completes assignments, and tracks progress |
| Individual subscriber | Purchases and uses personal resource access outside a school account |
| Parent/guardian | Optional role for purchasing a learner plan and viewing limited progress |

---

## 3. Technology stack

### Backend

- PHP 8.3 or newer
- Laravel 13
- Laravel Sanctum for future API and mobile authentication
- Spatie Laravel Permission for roles and permissions
- Laravel queues, scheduler, notifications, policies, events, and filesystem

### Frontend

- Blade templates
- Livewire 4
- Filament 5 for administrative panels
- Tailwind CSS 4
- Alpine.js where lightweight browser interactivity is needed
- Vite 7

### Data and infrastructure

- SQLite for quick local development
- MySQL 8+ or PostgreSQL 16+ for staging and production
- Redis recommended for production cache, sessions, rate limiting, and queues
- S3-compatible private object storage recommended for protected resources
- Nginx and PHP-FPM for production hosting
- Docker Compose configuration included for a reproducible development environment

### Document reading

Recommended integrations for later implementation:

- PDF.js for PDF reading
- EPUB.js for EPUB reading
- Server-generated expiring access links
- User or school watermarks
- Copy, printing, and download rules based on the resource licence

---

## 4. Repository structure

```text
literahub-platform/
├── app/
│   ├── Contracts/              Provider-independent service contracts
│   ├── Enums/                  Roles, statuses, and shared domain values
│   ├── Models/                 Initial Eloquent domain models
│   ├── Providers/              Laravel service bindings
│   └── Services/               Payment and infrastructure implementations
├── bootstrap/                  Laravel application bootstrap
├── config/                     Application and provider configuration
├── database/
│   ├── factories/              Model factories
│   ├── migrations/             Initial database schema
│   └── seeders/                Starter subscription-plan data
├── docker/                     Nginx and container configuration
├── docs/
│   ├── ARCHITECTURE.md         System architecture and boundaries
│   ├── DATABASE.md             Database design notes
│   ├── DEVELOPMENT_WORKFLOW.md Branching and engineering workflow
│   ├── PRODUCT_REQUIREMENTS.md Product scope and user requirements
│   ├── ROADMAP.md              Recommended implementation roadmap
│   ├── SECURITY.md             Security and protected-content guidance
│   └── assets/                 Interface concept image
├── public/                     Public web root
├── resources/
│   ├── css/                    Frontend styles
│   ├── js/                     Frontend JavaScript
│   └── views/                  Starter Blade interfaces
├── routes/                     Web, API, and console routes
├── storage/                    Runtime files and logs
├── tests/                      Initial automated tests
├── .env.example               Environment configuration template
├── docker-compose.yml         Development services
├── Dockerfile                 PHP application image
├── Makefile                   Common commands
├── composer.json              PHP dependencies and scripts
└── package.json               Frontend dependencies and scripts
```

---

## 5. Current implementation

The starter currently includes:

- Laravel 13 application bootstrap
- User-role and subscription-status enums
- Initial models for users, schools, resources, plans, subscriptions, payments, and reading progress
- Initial migrations for the above domains
- Seed data for example subscription plans
- Provider-independent payment contract
- Initial M-Pesa service placeholder
- Protected-resource filesystem configuration
- Starter landing, pricing, and dashboard views
- Docker, Nginx, MySQL, and Redis configuration
- GitHub Actions test workflow
- Product, architecture, database, security, workflow, and roadmap documentation
- Basic feature test for the public home page

### Not yet complete

The following must be implemented before production use:

- Registration, login, password reset, and email/phone verification screens
- Full role and permission seeding
- Filament administrator and school panels
- Complete school membership, class, teacher, and student modules
- Resource upload, publication, versioning, entitlement, and approval workflows
- Secure PDF/EPUB reader
- Bookmarks, highlights, reader notes, and offline policies
- Production M-Pesa authentication, STK Push, validation, and callback verification
- Card-payment provider integration
- Invoices, receipts, refunds, and reconciliation
- Assignments, quizzes, grading, submissions, and certificates
- Notifications through email, SMS, WhatsApp, and in-app channels
- Complete audit logging, account-sharing controls, watermarking, and abuse detection
- Production deployment hardening and load testing

---

## 6. Local installation: Windows

### Prerequisites

Install:

1. PHP 8.3 or newer with the required extensions
2. Composer 2.8 or newer
3. Node.js 20 or newer
4. Git
5. MySQL 8+ when not using SQLite

Recommended PHP extensions:

```text
bcmath
ctype
curl
dom
fileinfo
filter
intl
json
mbstring
openssl
pdo
pdo_mysql
pdo_sqlite
session
tokenizer
xml
zip
```

### PowerShell installation

```powershell
# Extract the ZIP, then enter the project directory
cd literahub-platform

# Install PHP dependencies
composer install

# Create the environment file
Copy-Item .env.example .env

# Generate the encryption key
php artisan key:generate

# Ensure the local SQLite file exists
New-Item database/database.sqlite -ItemType File -Force

# Create database tables and seed starter plans
php artisan migrate --seed

# Install frontend dependencies
npm install

# Build frontend assets
npm run build

# Run the application
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

For active frontend development, use two terminals:

```powershell
php artisan serve
```

```powershell
npm run dev
```

---

## 7. Local installation: Linux or macOS

```bash
cd literahub-platform
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Open `http://127.0.0.1:8000`.

For active development:

```bash
npm run dev
```

---

## 8. MySQL configuration

Create a database:

```sql
CREATE DATABASE literahub
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

Update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=literahub
DB_USERNAME=root
DB_PASSWORD=your_password
```

Then run:

```bash
php artisan config:clear
php artisan migrate:fresh --seed
```

Never run `migrate:fresh` against a production database because it deletes all existing tables and data.

---

## 9. Docker setup

The repository includes a Docker-based environment for developers who do not want to configure PHP, MySQL, and Redis directly on their computers.

### Start containers

```bash
docker compose up -d --build
```

### Install dependencies and initialise the project

```bash
docker compose exec app composer install
docker compose exec app cp .env.example .env
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Install and build frontend dependencies on the host machine:

```bash
npm install
npm run build
```

Inspect `docker-compose.yml` before first use and make sure the `.env` database host matches the Docker service name, normally:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=literahub
DB_USERNAME=literahub
DB_PASSWORD=secret

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=redis
```

---

## 10. Environment variables

Copy `.env.example` to `.env`. Never commit the real `.env` file.

### Application

```env
APP_NAME=LiteraHub
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
```

Production must use:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
```

### Files and protected resources

```env
FILESYSTEM_DISK=local
RESOURCE_FILESYSTEM_DISK=local
```

For production, configure an S3-compatible private disk and set:

```env
RESOURCE_FILESYSTEM_DISK=s3
```

Protected books must not be placed directly in `public/`.

### Queues

Development:

```env
QUEUE_CONNECTION=database
```

Production recommendation:

```env
QUEUE_CONNECTION=redis
```

Run a worker during development:

```bash
php artisan queue:work
```

Production workers should be managed by Supervisor, systemd, or the hosting platform.

### Scheduler

During local development:

```bash
php artisan schedule:work
```

Production cron entry:

```cron
* * * * * cd /path/to/literahub-platform && php artisan schedule:run >> /dev/null 2>&1
```

### Mail

Local development logs email instead of sending it:

```env
MAIL_MAILER=log
```

Configure SMTP or a transactional email provider before production launch.

---

## 11. M-Pesa setup

The project includes a service boundary for M-Pesa, but production payment processing is not complete.

Set these values in `.env` after obtaining credentials from the provider:

```env
PAYMENT_GATEWAY=mpesa
MPESA_ENVIRONMENT=sandbox
MPESA_CONSUMER_KEY=
MPESA_CONSUMER_SECRET=
MPESA_SHORTCODE=
MPESA_PASSKEY=
MPESA_CALLBACK_URL="${APP_URL}/api/payments/mpesa/callback"
```

A secure payment flow must:

1. Create an internal invoice before starting payment.
2. Send the payment request from the server, never directly from browser JavaScript.
3. Store the provider request identifiers.
4. Verify the callback authenticity and transaction status.
5. Make callback processing idempotent so duplicate callbacks cannot activate a subscription twice.
6. Activate access only after confirmed payment.
7. Record the complete transaction and audit trail.
8. Generate a receipt.
9. Handle timeouts, cancelled requests, failed transactions, refunds, and reconciliation.

Do not activate a subscription merely because the browser displays a successful message.

---

## 12. Roles and permissions

Recommended initial roles:

```text
super_admin
platform_admin
content_manager
author
school_admin
teacher
student
individual_subscriber
finance
support
```

Use policies and permissions for every protected action. Do not rely only on hiding buttons in the interface.

Examples:

- Only content managers may publish a resource.
- Only authorised school administrators may add users to their school.
- Teachers may assign only resources available under their school subscription.
- Students may read only resources covered by an active entitlement.
- Finance users may view payments but should not automatically gain content-management access.

---

## 13. Multi-tenancy strategy

The initial recommendation is **shared-database logical tenancy**:

- Every institution-scoped record contains a `school_id` or equivalent tenant reference.
- Queries are restricted by policies, scopes, and service-layer rules.
- Platform administrators can work across schools only through explicit permissions.

Do not create a separate database for every school during the MVP. It increases deployment, migration, backup, and reporting complexity. Separate databases can be considered later for enterprise customers with strict contractual isolation requirements.

---

## 14. Resource protection

Copyright protection is a core requirement. Implement the following before publishing licensed content:

- Store original files in private object storage
- Authorise every read and download request
- Use short-lived signed links or stream files through an authorised endpoint
- Add visible or forensic user/school watermarks where permitted
- Log resource views, downloads, devices, and unusual access patterns
- Restrict simultaneous sessions
- Rate-limit high-risk endpoints
- Define print, copy, text-to-speech, and download permissions per resource
- Maintain licence and distribution-right records
- Provide copyright complaint and takedown processes

A web application cannot fully prevent screenshots. The practical objective is controlled access, deterrence, accountability, and leak tracing.

---

## 15. Development commands

```bash
# Start Laravel development server
php artisan serve

# Start Vite development server
npm run dev

# Build production frontend assets
npm run build

# Run database migrations
php artisan migrate

# Reset development database and seed it
php artisan migrate:fresh --seed

# Run queue worker
php artisan queue:work

# Run scheduler locally
php artisan schedule:work

# Clear cached application state
php artisan optimize:clear

# Run automated tests
php artisan test

# Check Laravel routes
php artisan route:list

# Apply Laravel code formatting
./vendor/bin/pint
```

On Windows PowerShell, Pint can be run with:

```powershell
vendor\bin\pint
```

---

## 16. Testing requirements

Every major module should include:

- Unit tests for domain rules
- Feature tests for web and API endpoints
- Permission and tenant-isolation tests
- Payment callback and idempotency tests
- Subscription expiry and renewal tests
- Resource-entitlement tests
- File-access security tests
- Browser tests for high-value flows when practical

Before merging work:

```bash
php artisan test
npm run build
./vendor/bin/pint --test
```

The included GitHub Actions workflow should be expanded as dependencies and services are completed.

---

## 17. Recommended development sequence

### Phase 1: Foundation

1. Install and verify the starter application.
2. Add authentication scaffolding.
3. Configure Spatie roles and permissions.
4. Complete school membership and tenant scopes.
5. Add audit logging.
6. Create Filament platform-admin and school-admin panels.

### Phase 2: Content library

1. Add authors, publishers, categories, education levels, subjects, and editions.
2. Implement private resource uploads.
3. Add resource approval and publication workflows.
4. Implement subscription-tier entitlements.
5. Build catalogue, search, filters, and resource details.

### Phase 3: Billing

1. Complete plans, invoices, payments, and subscriptions.
2. Implement M-Pesa sandbox integration.
3. Add verified and idempotent callbacks.
4. Add card and manual bank-payment adapters.
5. Add receipts, renewal reminders, expiry, and grace periods.

### Phase 4: Reader and learning

1. Build PDF and EPUB readers.
2. Add progress, bookmarks, notes, and highlights.
3. Add classes and reading lists.
4. Add assignments, quizzes, submissions, and grading.
5. Add reporting dashboards.

### Phase 5: Pilot and launch

1. Perform security and load testing.
2. Import a limited approved content catalogue.
3. Pilot with two to five schools.
4. Measure onboarding, payment, reading, and support performance.
5. Resolve critical defects before wider launch.

See [`docs/ROADMAP.md`](docs/ROADMAP.md) for the detailed 24-week roadmap.

---

## 18. Git workflow

Recommended branches:

```text
main       Production-ready code
develop    Integrated development work
feature/*  New modules and features
fix/*      Bug fixes
hotfix/*   Urgent production fixes
```

Example:

```bash
git checkout -b feature/school-registration
# make changes
git add .
git commit -m "feat: add school registration"
git push -u origin feature/school-registration
```

Open a pull request into `develop`, review and test it, then merge. Promote tested releases from `develop` to `main`.

---

## 19. Publishing to GitHub

Create an empty private repository named `literahub-platform` under the appropriate Ligco Technologies or GitHub account.

```bash
git init
git add .
git commit -m "chore: initialise LiteraHub platform"
git branch -M main
git remote add origin https://github.com/OWNER/literahub-platform.git
git push -u origin main
```

Using GitHub CLI:

```bash
gh auth login
gh repo create literahub-platform --private --source=. --remote=origin --push
```

Do not commit:

- `.env`
- API secrets
- payment credentials
- production database exports
- private books or licensed resources
- user uploads
- `vendor/`
- `node_modules/`

---

## 20. Production deployment checklist

Before launch:

- Use a dedicated production domain with HTTPS
- Set `APP_ENV=production` and `APP_DEBUG=false`
- Use strong database and Redis credentials
- Configure private S3-compatible storage
- Run queue workers under a process manager
- Configure the scheduler
- Configure real mail and SMS providers
- Encrypt and back up databases and protected files
- Test backup restoration
- Add application, uptime, error, and infrastructure monitoring
- Configure rate limiting and a web application firewall where available
- Configure session and device policies
- Review all roles and permissions
- Verify all payment callbacks and reconciliation
- Run penetration, permission, tenant-isolation, and load tests
- Prepare privacy, copyright, acceptable-use, subscription, refund, and support policies
- Document incident-response and account-recovery procedures

Typical deployment commands:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize
php artisan queue:restart
```

`storage:link` should only expose intentionally public assets such as public profile images or covers. Protected books must remain on a private disk.

---

## 21. Backup and maintenance

Recommended minimum:

- Daily encrypted database backup
- Versioned private-object-storage backup
- Weekly restoration test or automated restore verification
- Retention policy covering daily, weekly, and monthly recovery points
- Centralised logs and error monitoring
- Monthly dependency and security review
- Regular access review for platform administrators

Maintenance mode:

```bash
php artisan down --secret="temporary-access-token"
# deploy and migrate
php artisan up
```

---

## 22. Troubleshooting

### `PHP was not found`

Install PHP and make sure its directory is included in the system `PATH`. Restart the terminal after updating environment variables.

### `composer was not found`

Install Composer globally and verify:

```bash
composer --version
```

### `APP_KEY is missing`

```bash
php artisan key:generate
```

### SQLite database errors

Make sure the file exists:

```bash
touch database/database.sqlite
```

PowerShell:

```powershell
New-Item database/database.sqlite -ItemType File -Force
```

### MySQL access denied

Check `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`, then run:

```bash
php artisan config:clear
```

### Vite manifest not found

```bash
npm install
npm run build
```

During development, keep `npm run dev` running.

### Changes to `.env` are ignored

```bash
php artisan optimize:clear
```

### Queue jobs do not run

```bash
php artisan queue:work
```

Then verify the queue connection and failed jobs:

```bash
php artisan queue:failed
```

---

## 23. Documentation

- [Product requirements](docs/PRODUCT_REQUIREMENTS.md)
- [Architecture](docs/ARCHITECTURE.md)
- [Database design](docs/DATABASE.md)
- [Security](docs/SECURITY.md)
- [Development workflow](docs/DEVELOPMENT_WORKFLOW.md)
- [Roadmap](docs/ROADMAP.md)

---

## 24. Ownership and licence

Copyright © 2026 **Ligco Technologies**.

This project is private and proprietary. Source code, designs, documentation, and business logic may not be copied, redistributed, sublicensed, sold, or deployed for third parties without written permission from Ligco Technologies and the project owner.

Third-party libraries remain subject to their respective licences.

---

## 25. Author

**Ligco Technologies**  
Software, ERP, POS, cloud, and digital-platform development.

Project stewardship, implementation, and technical documentation are attributed to Ligco Technologies.
