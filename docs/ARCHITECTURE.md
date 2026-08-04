# Architecture

## Style

LiteraHub starts as a modular monolith. It has one Laravel deployment and one relational database, while business logic is separated into clear domains. This reduces operational complexity without creating an unstructured application.

## Logical components

```text
Browser / Installable PWA
        |
        v
Laravel Web + Livewire + Filament
        |
        +-- Accounts and permissions
        +-- Schools and memberships
        +-- Content catalogue and publishing
        +-- Reader and progress
        +-- Subscriptions and entitlements
        +-- Payments and invoicing
        +-- Assignments and assessments
        +-- Reporting and notifications
        |
        +-- MySQL/PostgreSQL
        +-- Redis or database queue
        +-- Private object storage
        +-- M-Pesa/card providers
        +-- Email/SMS services
```

## Multi-tenancy

Use shared tables with `school_id` boundaries rather than a separate database per school. Every school-owned record must be scoped by policy and query. Platform administrators may cross tenant boundaries; school users may not.

## Infrastructure independence

- Storage is accessed through Laravel filesystem disks.
- Queued jobs use Laravel's queue contracts.
- Payments use the `PaymentGateway` interface.
- Notifications use Laravel notifications.
- Provider credentials remain in environment variables.
- Controllers do not directly call provider SDKs.

## Deployment evolution

1. Local SQLite and database queues
2. Single VPS with MySQL, Redis and object storage
3. Managed database, multiple app instances, CDN and dedicated queue workers
