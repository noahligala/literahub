# LiteraHub Platform

**Author:** Ligco Technologies  
**Project type:** Subscription-based digital literature and education SaaS  
**Primary framework:** Laravel 13  
**Current stage:** Active MVP development / product demonstration build  
**Licence:** Proprietary

LiteraHub is a web-first digital literature and academic learning platform for schools, teachers, students, authors, publishers, platform administrators, and individual subscribers.

The platform combines a protected digital library with school licensing, content review, teacher assignment workflows, online reading, borrowing, bookmarking, user management, reporting, and future payment/subscription automation.

The current implementation has progressed beyond the original starter architecture. Core content-management, school licensing, school-library access, protected reading, borrowing, bookmarking, role routing, and the first assignment-management workflows are operational.

The application is implemented as a **modular Laravel monolith**. This keeps the MVP affordable and maintainable while allowing infrastructure services such as payments, storage, queues, notifications, caching, and databases to be changed later through Laravel abstractions and application services.

---

## 1. Product objectives

LiteraHub is designed to provide:

- Subscription and licensed-resource access for schools and individual subscribers
- A protected online literature library
- Publisher, author, book, review, publication, and licensing workflows
- School administration for students, teachers, classes, streams, subscriptions, and licences
- Teacher access to licensed institutional literature
- Class-based reading and assignment workflows
- Student borrowing, online reading, bookmarks, notes, and reading progress
- Approval-based access to licensed books outside a student's normal class scope
- Rights-aware print and download controls
- Configurable school and individual subscription models
- M-Pesa, card, and manual institutional payment support
- Invoices, receipts, renewals, expiry rules, and payment callbacks
- Auditable resource access and role-based security
- A responsive application for desktops, tablets, and phones
- Future PWA support

---

## 2. Current roles

The current role model is:

| Role | Primary responsibilities |
|---|---|
| `super_admin` | Full platform administration |
| `platform_admin` | Platform operations and administration |
| `content_manager` | Content review, approval, publication, and catalogue management |
| `author` | Author profile and permitted content workflows |
| `school_admin` | School users, classes, licences, subscription, and institutional reporting |
| `teacher` | Licensed library access, class teaching tools, assignments, and student activity |
| `student` | School library access, borrowing, reading, bookmarks, requests, and assignments |
| `individual_subscriber` | Personal library/subscription access outside a school |
| `finance` | Financial and licence-related administrative workflows |
| `support` | Support and platform assistance workflows |

### Current dashboard routing

```text
super_admin           -> /admin
platform_admin        -> /platform
content_manager       -> /staff
author                -> /staff
finance               -> /staff
support               -> /staff
school_admin          -> /school
teacher               -> /teacher
student               -> /school/library
individual_subscriber -> /library
```

All authenticated users initially pass through `/dashboard`, where `DashboardController` determines the appropriate portal.

---

## 3. Technology stack

### Backend

- PHP 8.3+ supported; current development environment uses PHP 8.4
- Laravel 13
- Laravel Fortify authentication
- Passkeys / two-factor authentication support through the current authentication stack
- Laravel Sanctum for future API/mobile authentication
- Spatie Laravel Permission
- Laravel queues
- Laravel scheduler
- Laravel notifications
- Laravel policies and middleware
- Laravel filesystem abstraction
- Eloquent ORM

### Frontend

- Blade
- Livewire 4
- Filament 5
- Tailwind CSS 4
- Alpine.js
- Vite 7
- PDF.js for the current online PDF reader

### Data and infrastructure

- SQLite for local development
- MySQL/MariaDB for the temporary cPanel demonstration environment
- MySQL 8+ or PostgreSQL recommended for long-term production
- Redis recommended for production sessions, cache, rate limiting, and queues
- S3-compatible private object storage recommended for protected books in production
- Apache/cPanel supported for MVP demonstration
- Nginx + PHP-FPM or equivalent managed application hosting recommended for production

---

## 4. Repository structure

```text
literahub/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   ├── Models/
│   ├── Services/
│   ├── Policies/
│   └── Providers/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── build/
│   ├── index.php
│   └── .htaccess
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
├── storage/
├── tests/
├── .env.example
├── artisan
├── composer.json
├── package.json
└── README.md
```

---

## 5. Current MVP implementation status

### Authentication and accounts

Implemented:

- Registration flows
- Login
- Logout
- Password reset
- Fortify-based authentication
- Two-factor authentication support
- Passkey-related routes
- Role-based dashboard routing
- Spatie role handling

### Platform administration

Implemented:

- Super Administrator dashboard
- Platform Administrator dashboard
- Staff dashboard
- Publishers CRUD
- Authors CRUD
- Books CRUD
- Book review workflow
- Book approval workflow
- Book publication workflow
- Book licence management

### Content lifecycle

The working content lifecycle is currently:

```text
Publisher
   ↓
Author
   ↓
Book Upload
   ↓
Review
   ↓
Approve / Request Changes / Reject
   ↓
Publish
   ↓
Issue School Licence
   ↓
Institutional Library
```

The platform can currently:

- Create and update publishers
- Create and update authors
- Upload books
- Store ISBN and book metadata
- Review books
- Approve books
- Publish books
- Reject or request changes
- Issue school licences
- Renew or revoke licences
- Update book metadata and rights

### School administration

Implemented or scaffolded:

- School dashboard
- Students
- Teachers
- Classes
- Streams
- School profile
- School library
- School book licences
- School licence catalogue
- Book access requests
- Assignments
- Subscription views
- Reporting views

### Teacher portal

Current teacher portal includes or exposes:

- Teacher dashboard
- Classes
- Shared institutional library
- Reading lists placeholder
- Assignments
- Students
- Performance

Teacher library access uses the same school-licensed catalogue rather than exposing the global catalogue.

### Student / learner portal

Current student flow includes:

- School-licensed library access
- Book details
- Borrowing
- Returning
- Online reading
- Bookmarking
- Access requests for permitted out-of-class books
- Assignment integration in progress

### Individual subscriber portal

Scaffolded routes currently include:

- Dashboard
- Browse
- Continue reading
- Bookmarks
- Notes
- Assignments
- Progress
- Subscription
- Profile

Individual subscriber entitlement rules still need to be fully defined and enforced.

---

## 6. Library and licensing rules

LiteraHub uses licence-aware catalogue access.

### Core rule

A school user must not see or consume a book merely because the book exists on the platform.

The access chain is:

```text
Book/IP Rights
      ↓
Active School Licence
      ↓
User Entitlement
      ↓
Role/Class/Approval Rules
      ↓
Read / Borrow / Assign / Download / Print
```

### School catalogue rules

- The school library contains only books covered by an active school licence.
- Expired or revoked licences remove normal school access.
- Students normally see published books assigned to their classes.
- A student may receive approved individual access to another school-licensed title.
- An unlicensed title should use the school-admin licence acquisition/request workflow.
- Teachers may only assign resources permitted by the active school licence and book rights.

### Rights hierarchy

The licence can narrow rights but must never expand the underlying book/IP rights.

Examples:

```text
Book says:
allow_download = false

School licence says:
download = true

Effective result:
download = false
```

Rights currently include or anticipate:

- online reading
- borrowing
- teacher assignment
- download
- printing
- loan duration
- concurrent loans
- school-specific rights restrictions

---

## 7. Book access and borrowing

The institutional library currently supports:

- Licensed-book filtering
- Search
- Category filtering
- Publisher and author metadata
- Book detail views
- Borrowing
- Returning
- Bookmark creation
- Licence validation
- role-aware access checks

The student catalogue restricts visibility to books that are:

1. covered by an active school licence;
2. published; and
3. either assigned to one of the student's classes or individually approved.

### Borrowing

Borrowing currently checks:

- student role
- book access
- duplicate active borrowing
- maximum concurrent loans
- configured loan duration

The borrowing record stores:

- user
- book
- school
- borrowed time
- due time
- returned time
- status

---

## 8. Protected reader

The current MVP reader uses **PDF.js**.

Current reader features include:

- authenticated reader route
- protected stream endpoint
- previous/next page navigation
- page number navigation
- zoom
- bookmarks
- optional download
- optional print
- rights-aware reader controls
- PDF outline / contents support where available
- PDF.js WebAssembly/JBIG2 support assets

Current protected reader routes include:

```text
/reader/{book}
/reader/{book}/stream
/reader/{book}/download
/reader/{book}/print
```

### Important MVP limitation

The current PDF.js reader still sends authorised PDF content to the browser for rendering.

A stronger architecture has already been designed for later development:

```text
Original PDF
    ↓
Private Storage
    ↓
Backend Authorisation
    ↓
Page Rendering Service
    ↓
Secure Page API
    ↓
Browser Reader
```

The stronger server-rendered architecture is intentionally postponed while the MVP learning and demonstration flows are completed.

### Protected content requirements

Original protected books should not be stored directly in `public/`.

Preferred location:

```text
storage/app/private/
```

or a private S3-compatible object-storage bucket.

---

## 9. Assignments

Assignment management is now under active implementation.

### Existing assignment model

Assignments currently contain:

- school
- class
- creator
- resource/book reference
- title
- instructions
- due date
- status

Assignment-student membership is represented by the `assignment_student` pivot.

Existing pivot state includes:

- status
- score
- submitted time

### MVP assignment expansion

The current assignment workflow is being expanded to include:

- licensed book selector
- available-from date
- due date
- start page
- end page
- total marks
- assignment status
- written learner response
- teacher feedback
- grading information

Target MVP workflow:

```text
Teacher
   ↓
Choose Licensed Book
   ↓
Choose Class
   ↓
Create Assignment
   ↓
Publish
   ↓
Students Receive Assignment
   ↓
Open Assigned Book
   ↓
Read Required Range
   ↓
Submit Response
   ↓
Teacher Reviews
   ↓
Marks + Feedback
```

### Assignment statuses

Current assignment statuses:

```text
draft
published
closed
archived
```

---

## 10. Multi-tenancy strategy

LiteraHub currently uses **shared-database logical tenancy**.

Institution-scoped records include a `school_id` or equivalent relationship.

Important protections:

- School membership must be active.
- Controllers and services scope queries by school.
- School users must not receive another school's licences, classes, students, assignments, or reading records.
- Platform administrators may cross school boundaries only through explicit permissions.

Do not create a separate database per school during the MVP.

---

## 11. Local development

### Windows prerequisites

Install:

1. PHP 8.3+
2. Composer
3. Node.js 20+
4. Git
5. SQLite or MySQL

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

### Windows PowerShell setup

```powershell
composer install

Copy-Item .env.example .env

php artisan key:generate

New-Item database/database.sqlite -ItemType File -Force

php artisan migrate --seed

npm install

npm run build

php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

For active frontend work use two terminals:

```powershell
php artisan serve
```

and:

```powershell
npm run dev
```

---

## 12. MySQL configuration

Example production/demo configuration:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=cpaneluser_literahub
DB_USERNAME=cpaneluser_literahub
DB_PASSWORD=your_secure_password
```

Then:

```bash
php artisan optimize:clear
php artisan migrate --force
```

Never use:

```bash
php artisan migrate:fresh
```

against production or a persistent demonstration database unless intentionally resetting all data.

---

## 13. Environment configuration

### Local

```env
APP_NAME="LiteraHub"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
```

### Demo / production-style environment

```env
APP_NAME="LiteraHub"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://literahub.example.com
```

Never commit the real `.env`.

---

## 14. File storage

### Public assets

Appropriate public assets include:

- publisher logos
- book covers
- profile images
- intentionally public media

Laravel public storage:

```text
storage/app/public/
```

with:

```bash
php artisan storage:link
```

### Protected content

Protected literature should remain on a private disk:

```text
storage/app/private/
```

Do not expose protected books through a direct public path.

A protected book should be accessed through authorised Laravel endpoints.

---

## 15. Temporary cPanel MVP deployment

LiteraHub is currently suitable for temporary product demonstrations on shared cPanel hosting where PHP 8.3/8.4 and MySQL are available.

### Important

On cPanel/Apache, do **not** use:

```bash
php artisan serve
```

Apache serves the application automatically.

### Recommended commands

```bash
composer install --no-dev --optimize-autoloader

php artisan migrate --force

php artisan storage:link

php artisan optimize:clear

php artisan config:cache

php artisan route:cache

php artisan view:cache
```

Build frontend assets locally if Node.js is unavailable on cPanel:

```powershell
npm install
npm run build
```

Upload:

```text
public/build/
```

to the server.

### Current shared-hosting layout

For the temporary MVP demonstration, the entire project may currently exist below the web-accessible account path.

This is **not the preferred final production configuration**.

The preferred long-term configuration is:

```text
DocumentRoot -> /path/to/literahub/public
```

For the temporary demo, Apache `.htaccess` rules must:

- disable directory indexing
- deny hidden files
- deny access to internal Laravel directories
- deny `.env`
- deny `composer.json`
- deny `artisan`
- deny `vendor`
- deny `storage`
- route all application URLs through `public/index.php`

### Root `.htaccess`

A root-level protection/rewrite configuration is required when the project root is temporarily web-accessible.

The root `.htaccess` should route normal requests directly into Laravel's front controller while blocking internal paths.

### `public/.htaccess`

The `public/` directory must contain the normal Laravel Apache rewrite configuration so application routes can resolve correctly.

Without working Apache rewrite rules, the home page may load while URLs such as these return 404:

```text
/login
/dashboard
/school/library
/school/assignments
/teacher
```

### Route verification

Use:

```bash
php artisan route:list
```

If routes appear in `route:list` but Apache returns a 404, investigate `.htaccess`, `mod_rewrite`, `AllowOverride`, or document-root configuration rather than modifying Laravel routes.

---

## 16. cPanel queue and scheduler strategy

Shared hosting may not allow a permanent worker process.

For the MVP, database queues can be processed using cron:

```cron
* * * * * cd /home/USERNAME/path-to-literahub && php artisan queue:work --stop-when-empty --tries=3 >> /dev/null 2>&1
```

Laravel scheduler:

```cron
* * * * * cd /home/USERNAME/path-to-literahub && php artisan schedule:run >> /dev/null 2>&1
```

For long-term production, use a persistent worker managed by Supervisor, systemd, container orchestration, or a managed application platform.

---

## 17. Payments

The architecture includes payment abstraction for:

- M-Pesa
- cards
- manual institutional payments

Production payment processing is not yet complete.

A secure payment flow must:

1. Create an internal invoice.
2. Start payment from the server.
3. Store provider request identifiers.
4. Verify callbacks.
5. Make callback processing idempotent.
6. Activate access only after confirmed payment.
7. Record transaction and audit information.
8. Generate a receipt.
9. Handle failures, cancellations, timeouts, refunds, and reconciliation.

Do not activate a subscription based only on a browser success message.

---

## 18. Security requirements

Security is a core LiteraHub requirement.

Current and planned protections include:

- role-based access control
- active school membership checks
- school/tenant scoping
- active licence checks
- book-right enforcement
- protected resource streaming
- download/print restrictions
- session controls
- rate limiting
- reader activity logging
- future device registration
- future forensic watermarks
- future server-rendered protected pages
- audit logs
- abuse detection

### Important limitations

No browser-based reader can fully prevent:

- screenshots
- photography of the screen
- determined client-side extraction

The objective is:

```text
controlled access
+ deterrence
+ accountability
+ leak tracing
```

rather than claiming impossible absolute prevention.

---

## 19. Development commands

```bash
# Start Laravel locally
php artisan serve

# Start Vite
npm run dev

# Production frontend build
npm run build

# Run migrations
php artisan migrate

# Development reset
php artisan migrate:fresh --seed

# Queue worker
php artisan queue:work

# Scheduler
php artisan schedule:work

# Clear application caches
php artisan optimize:clear

# Cache production configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache Blade templates
php artisan view:cache

# Show routes
php artisan route:list

# Tests
php artisan test

# Format PHP
./vendor/bin/pint
```

Windows:

```powershell
vendor\bin\pint
```

---

## 20. Testing priorities

Every major module should eventually include:

- unit tests
- feature tests
- role/permission tests
- tenant-isolation tests
- licence-entitlement tests
- reader access tests
- borrowing lifecycle tests
- concurrent-loan tests
- assignment workflow tests
- assignment submission/grading tests
- payment callback/idempotency tests
- subscription expiry tests
- file access/security tests
- browser tests for critical workflows

Before merging:

```bash
php artisan test
npm run build
./vendor/bin/pint --test
```

---

## 21. Current MVP roadmap

### Completed / operational foundation

- Laravel application
- authentication
- roles
- dashboards
- publishers
- authors
- books
- review workflow
- publication workflow
- licence issuance
- licence renewal/revocation
- school library
- student class-aware access
- access requests
- borrowing
- returning
- bookmarks
- PDF.js protected reader
- basic school management
- assignment CRUD foundation
- temporary cPanel demonstration deployment

### Current priority

**Teacher → Student assignment workflow**

Work currently includes:

1. licensed-book assignment
2. reading ranges
3. assignment start/due dates
4. marks
5. student assignment list
6. student response
7. teacher grading
8. feedback

### Next MVP priorities

1. Complete assignment submission and grading
2. Continue-reading history
3. Student bookmark dashboard
4. Teacher learner-activity views
5. School admin reporting
6. Subscription/payment status
7. M-Pesa sandbox integration
8. Demo data and end-to-end testing
9. Security hardening
10. Pilot deployment

### Deferred until after MVP

- automatic PDF TOC generation
- OCR/AI chapter detection
- server-rendered page reader
- full device-registration system
- forensic watermark pipeline
- advanced quizzes
- plagiarism detection
- AI marking
- offline/PWA reading
- native mobile applications
- advanced analytics

---

## 22. Demonstration flow

The target MVP demonstration should show:

```text
Super Admin
   ↓
Create / Manage Publisher
   ↓
Create / Manage Author
   ↓
Upload Book
   ↓
Review
   ↓
Approve
   ↓
Publish
   ↓
Issue School Licence
   ↓
Teacher Logs In
   ↓
Browse Licensed Library
   ↓
Create Assignment
   ↓
Student Logs In
   ↓
View School Library
   ↓
Borrow Book
   ↓
Read Online
   ↓
Bookmark
   ↓
Open Assignment
   ↓
Submit Work
   ↓
Teacher Grades + Feedback
```

This is the primary product story for the MVP.

---

## 23. Demo data

Development/demo environments may contain seeded accounts for:

- super administrator
- platform administrator
- content manager
- finance
- support
- authors
- school administrator
- teachers
- students
- individual reader

Demo credentials must never be reused in production.

A dedicated `DemoSeeder` is recommended so the demonstration environment can be recreated safely without relying on broad development seeders.

---

## 24. Git workflow

Recommended branches:

```text
main
develop
feature/*
fix/*
hotfix/*
```

Example:

```bash
git checkout -b feature/assignment-submissions

git add .

git commit -m "feat: add student assignment submission workflow"

git push -u origin feature/assignment-submissions
```

Open a pull request into `develop`, test, then promote stable releases to `main`.

Do not commit:

- `.env`
- secrets
- API keys
- payment credentials
- production database exports
- private copyrighted books
- user uploads
- `vendor/`
- `node_modules/`

---

## 25. Production-readiness checklist

Before a public production launch:

- dedicated production domain
- HTTPS everywhere
- `APP_ENV=production`
- `APP_DEBUG=false`
- secure MySQL/PostgreSQL credentials
- Redis or equivalent production cache/session backend
- private S3-compatible protected storage
- production queue workers
- scheduler
- production email
- M-Pesa/card integration
- backups
- restoration testing
- application monitoring
- error monitoring
- rate limiting
- WAF where practical
- audit logging
- tenant isolation review
- role/permission review
- licence-rule tests
- reader-access tests
- payment reconciliation
- penetration testing
- load testing
- privacy policy
- copyright policy
- acceptable-use policy
- subscription/refund terms
- incident-response process

---

## 26. Troubleshooting

### Home page works but subpages return 404

Run:

```bash
php artisan route:list
```

If Laravel shows the routes but Apache returns 404, check:

- root `.htaccess`
- `public/.htaccess`
- Apache `mod_rewrite`
- `AllowOverride`
- hosting document root

### `APP_KEY` missing

```bash
php artisan key:generate
```

### Environment changes ignored

```bash
php artisan optimize:clear
```

### Vite manifest missing

```bash
npm install
npm run build
```

### MySQL access denied

Verify:

```text
DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD
```

then:

```bash
php artisan config:clear
```

### File upload temporary directory error

On environments where PHP reports:

```text
File upload error - unable to create a temporary file
```

verify PHP's configured upload temporary directory is valid and writable.

Development example:

```ini
file_uploads = On
upload_tmp_dir = "C:\php-temp\uploads"
sys_temp_dir = "C:\php-temp"
upload_max_filesize = 20M
post_max_size = 25M
```

### Queue jobs do not run

```bash
php artisan queue:failed
php artisan queue:work
```

On cPanel, verify the cron-based worker.

---

## 27. Ownership and licence

Copyright © 2026 **Ligco Technologies**.

LiteraHub is private and proprietary software.

Source code, designs, business logic, documentation, protected content workflows, and deployment artefacts may not be copied, redistributed, sublicensed, sold, or deployed for third parties without written permission from Ligco Technologies and the project owner.

Third-party libraries remain subject to their respective licences.

Authors and publishers retain intellectual-property rights in their content according to the rights and distribution agreements recorded by the platform.

---

## 28. Project stewardship

**Ligco Technologies**  
Software, ERP, POS, cloud, and digital-platform development.

LiteraHub product design, engineering, implementation, architecture, security model, and technical documentation are managed under Ligco Technologies.

---

## 29. Current project note

This README reflects the active MVP as of the current development cycle.

The project is under continuous development. Sections describing assignments, subscriptions, payments, reporting, secure content rendering, and production infrastructure should be updated as those modules move from scaffolded or partial state to complete implementation.
