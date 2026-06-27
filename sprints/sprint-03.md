# Sprint 03 — Ticket & Comment API

**Date:** 2026-06-27
**Goal:** Build the core ticket and comment API endpoints.
**Orchestrator:** Hermes (z-ai/glm-5.1 via EastRouter)
**Coder:** OpenClaw (moonshotai/kimi-k2.7-code via EastRouter)

---

## Issues

| ID | Title | Status |
|----|-------|--------|
| S03-01 | Ticket CRUD: index/store/show/update with filters & activity logging | DONE |
| S03-02 | Comment API: store() on tickets with is_internal flag | DONE |
| S03-03 | ActivityLog: log status/priority/assignee changes on update | DONE |

---

## Handoffs (Hermes → OpenClaw in #agent-coder)

1. **S03-01** — Hermes assigned Ticket CRUD → OpenClaw built `TicketController.php` with org-scoped queries, filterable by `?status=` and `?priority=`. Committed: `feat(tickets): add CRUD endpoints for tickets` (`8a0fa5a`)
2. **S03-02** — Hermes assigned Comment API → OpenClaw built `CommentController::store()` with validation, org-scoped creation, `author_id` binding. Committed: `feat(comments): add comment creation endpoint` (`b2d2f59`)
3. **S03-03** — Hermes assigned ActivityLog → OpenClaw added `ActivityLog` writes in `TicketController::update()` tracking `status`, `priority`, `assignee_id` changes with `meta {from,to}`. Committed: `feat(activity): log ticket changes to activity_log` (`9ad5658`)

---

## Outcome

- `GET /api/tickets` — list with status/priority filters, paginated, org-scoped
- `POST /api/tickets` — create ticket
- `GET /api/tickets/{ticket}` — single ticket with requester, assignee, comments, activityLogs
- `PUT /api/tickets/{ticket}` — update with automatic activity logging
- `POST /api/tickets/{ticket}/comments` — create comment (body, is_internal flag)
- All routes protected by `auth:sanctum`
- OrganizationScope auto-filters all queries by auth user's org

---

## Commits in this sprint

- `8a0fa5a` feat(tickets): add CRUD endpoints for tickets
- `b2d2f59` feat(comments): add comment creation endpoint
- `9ad5658` feat(activity): log ticket changes to activity_log
