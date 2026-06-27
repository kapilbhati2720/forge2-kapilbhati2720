# Sprint 01 — Project Initialization & Multi-Tenant Core

**Goal:** Bootstrap Laravel and React projects, define database migrations with organization isolation scoping, configure Sanctum authorization, and set up CI quality gates.

## Tasks & Backlog
- [ ] Initialize git repository and create initial commit.
- [ ] Scaffold Laravel 11 backend via `composer create-project`.
- [ ] Scaffold React 19 + Vite frontend via `npm create-vite`.
- [ ] Setup MySQL database migrations:
  - `organizations`
  - `users` (linked to `organizations`)
  - `tickets` (scoped by `organization_id`)
  - `comments` (scoped by `organization_id`)
- [ ] Implement Multi-Tenancy global query scope in Laravel models.
- [ ] Implement register/login Sanctum authentication controllers.
- [ ] Set up GitHub Actions CI workflow to run test suite on PRs.
- [ ] Verify local backend/frontend connectivity.
