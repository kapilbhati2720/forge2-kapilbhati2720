# Sprint 04 — Validation, Seeders & Tests

**Date:** 2026-06-27
**Goal:** Harden the API and seed demo data.
**Orchestrator:** Hermes (z-ai/glm-5.1 via EastRouter)
**Coder:** OpenClaw (moonshotai/kimi-k2.7-code via EastRouter)

---

## Issues

| ID | Title | Status |
|----|-------|--------|
| S04-01 | Form validation: StoreTicketRequest, UpdateTicketRequest, StoreCommentRequest | ASSIGNED |
| S04-02 | Seeder: 1 org, 1 admin, 2 agents, 2 customers, 12 tickets, 3 comments | OPEN |
| S04-03 | Feature tests: Pest tests for tickets, comments, tenant isolation | OPEN |

---

## Commits expected this sprint

- `feat(validation): add form request validation for tickets and comments`
- `feat(seed): add demo seeder with org, users, tickets, comments`
- `test(api): add feature tests for tickets, comments, and tenant isolation`
