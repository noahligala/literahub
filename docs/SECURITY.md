# Security baseline

- Store books and protected files on a private filesystem disk.
- Generate short-lived signed URLs only after policy and subscription checks.
- Add school/user watermarks to generated previews and downloads.
- Rate-limit authentication, reader endpoints and payment callbacks.
- Verify all callbacks server-to-server and make them idempotent.
- Encrypt sensitive payment payloads at rest.
- Log subscription changes, resource access and privileged administrator actions.
- Limit simultaneous sessions where licences require it.
- Use least-privilege roles and explicit Laravel policies.
- Never commit `.env`, credentials, private keys or production resources.
- Back up the database and object storage and test restoration.
- Run dependency, static-analysis and test checks in CI.

No web application can stop screenshots. Controls should focus on access restriction, deterrence, traceability and contractual enforcement.
