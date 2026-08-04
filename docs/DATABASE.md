# Initial data model

## Implemented starter tables

- users
- schools
- school_user
- subscription_plans
- subscriptions
- resources
- reading_progress
- payments
- sessions, jobs, cache and framework support tables

## Next tables

- classes and class_members
- authors and publishers, if authors are not represented solely as users
- resource_categories and resource_plan_entitlements
- chapters and resource_files
- bookmarks, highlights and reader_notes
- assignments, submissions and attachments
- quizzes, questions, options and attempts
- invoices and invoice_items
- payment_events and webhook_receipts
- notifications and audit_logs
- support_tickets

## Key constraints

- Transaction/provider references must be unique.
- School membership must be unique per user and school.
- Reading progress must be unique per user and resource.
- Resources must never expose an unrestricted public file path.
- Subscription entitlements must be evaluated server-side.
