# Sprint 5 — Frontend & Validation Polish

**Branch:** `feature/sprint-5-frontend`  
**Merged:** 2026-06-27

## Goals
- React frontend with protected routing (auth guard)
- Login / Register pages wired to `/api/login` and `/api/register`
- Ticket list page with status + priority filters
- Ticket detail page with comment thread
- Form request validation (StoreTicketRequest, UpdateTicketRequest, StoreCommentRequest)
- Demo seeder (1 org, 5 users, 12 tickets, 3 comments)

## Issues Completed

### S05-01 — Form Request Validation
- Created `StoreTicketRequest`, `UpdateTicketRequest`, `StoreCommentRequest`
- All validated fields return structured JSON errors
- **Commit:** `feat(validation): add form request classes for tickets and comments`

### S05-02 — Demo Seeder
- `DemoSeeder` creates 1 org, 1 admin, 2 agents, 2 customers
- Seeds 12 tickets across priority/status combinations
- Seeds 3 comments on first ticket
- **Commit:** `feat(seed): add DemoSeeder with realistic demo data`

### S05-03 — React Frontend
- `Login.jsx` and `Register.jsx` with JWT token stored in `localStorage`
- `PrivateRoute` wrapper guards `/tickets` and sub-routes
- `Tickets.jsx` — paginated ticket list with status/priority filter dropdowns
- `TicketDetail.jsx` — full thread with internal/public badge on comments
- `api.js` centralizes `apiFetch()` with `Authorization: Bearer` injection
- **Commit:** `feat(frontend): scaffold React auth, ticket list, ticket detail pages`

## Outcome
All routes functional. `npm run build` succeeds. Frontend served from Vite dev server connects to Laravel backend on port 8000.

**Status:** ✅ Merged to `main`
