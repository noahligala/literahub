# Development workflow

## Branching

- `main`: deployable code
- `develop`: optional integration branch during early build
- `feature/<name>`: individual features
- `fix/<name>`: defect corrections

## Pull requests

Every pull request should include:

- Problem and intended result
- Screenshots for interface changes
- Migration and rollback notes
- Security and tenant-isolation considerations
- Tests added or updated

## Definition of done

- Acceptance criteria met
- Automated tests pass
- Authorization checked
- No provider secrets committed
- Migrations include rollback logic
- Documentation updated
- Responsive interface reviewed
- Error states handled
