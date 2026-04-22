# Concerns & Debt

## Technical Debt
- **Large Files:** `repomix-output.xml` is tracked in Git, which can slow down operations and bloat the repository.
- **Workflow Automation:** The local `gsd-sdk` CLI encountered a `MODULE_NOT_FOUND` error, requiring the agent to manual follow GSD protocols.

## Security & Maintenance
- **Secrets:** Ensure `.env` files remain ignored (verified).
- **Index Freshness:** The GitNexus index requires periodic `npx gitnexus analyze` to reflect recent changes accurately.

## Consistency
- **Vietnamese UI:** Ensuring all user-facing strings are correctly accented and professional across all modules.
- **UX Parity:** Aligning the Admin and Student experience with the new GSD-driven roadmap.
