# PulseDesk — Agent Prompt Queue

> All prompts sent to OpenClaw via Hermes in #agent-coder.
> Rule: One prompt at a time. Wait for confirmation before next.
> Every prompt ends with a required commit message.

---

## STATUS KEY
- DONE
- IN PROGRESS
- QUEUED

---

## [DONE] P-01 — Migration Ordering + Fresh Run
Channel: #agent-coder
Task: Fix migration file order so tables run: organizations > add_org_id_to_users > tickets > comments > sla_policies > activity_logs. Run php artisan migrate:fresh --force.
Commit: feat(db): scaffold multi-tenant schema with org-scoped tables

---

## [DONE] P-02 — Eloquent Models
Channel: #sprint-main
Task: Scaffold Eloquent Models: Organization, Ticket, Comment, SlaPolicy, ActivityLog. Each needs fillable and relationships.
Commit: feat(models): add Organization, Ticket, Comment, SlaPolicy, ActivityLog models

---

## [DONE] P-03 — OrganizationScope Global Scope
Channel: #agent-coder
Task: Create app/Scopes/OrganizationScope.php auto-filtering by auth()->user()->organization_id. Apply to all tenant-scoped models. Use withoutTenantScope() escape hatch.
Commit: feat(auth): add OrganizationScope global scope for multi-tenancy

---

## [DONE] P-04 — Sanctum Auth
Channel: #agent-coder
Task: Set up Sanctum. Create AuthController with register() (creates User + Organization atomically) and login() (returns token). Add POST /api/register and POST /api/login to routes/api.php (unauthenticated).
Commit: feat(auth): add Sanctum register and login endpoints

---

## [QUEUED] P-05 — Ticket CRUD Controller
Channel: #agent-coder
Task: Create TicketController with index() (org-scoped, filterable by status/priority), store(), show(), update(). Add resource routes under auth:sanctum middleware in routes/api.php.
Commit: feat(tickets): add CRUD endpoints for tickets

---

## [QUEUED] P-06 — Comment Controller
Channel: #agent-coder
Task: Create CommentController with store() that creates comment on a ticket (is_internal flag). Route: POST /api/tickets/{ticket}/comments under sanctum.
Commit: feat(comments): add comment creation endpoint

---

## [QUEUED] P-07 — Activity Log on Ticket Update
Channel: #agent-coder
Task: In TicketController::update(), write ActivityLog entry with actor_id, action (status_changed/priority_changed/assigned), meta {from, to} after each change.
Commit: feat(activity): log ticket status, priority, and assignment changes

---

## [QUEUED] P-08 — Form Request Validation
Channel: #agent-coder
Task: Create StoreTicketRequest, UpdateTicketRequest, StoreCommentRequest with validation rules. Wire into controllers.
Commit: feat(validation): add form request validation for tickets and comments

---

## [QUEUED] P-09 — Database Seeder
Channel: #agent-coder
Task: Seed: 1 Organization (Acme Corp), 3 Users (admin/agent/customer), 5 Tickets (mixed status/priority), 3 Comments on ticket 1. Run php artisan db:seed.
Commit: feat(seed): add dev seeder with org, users, tickets, comments

---

## [QUEUED] P-10 — Frontend: Login + Register Pages
Channel: #agent-coder
Task: In frontend/src/pages create Login.jsx and Register.jsx. Call POST /api/login and /api/register via Axios. Store token in localStorage. Redirect to /tickets on success. Clean modern UI.
Commit: feat(frontend): add login and register pages with Sanctum token auth

---

## [QUEUED] P-11 — Frontend: Ticket List Page
Channel: #agent-coder
Task: Create Tickets.jsx with filterable list by status/priority. Each row shows subject, status badge, priority badge, assignee. Click navigates to /tickets/:id. Use Bearer token header.
Commit: feat(frontend): add ticket list page with status and priority filters

---

## [QUEUED] P-12 — Frontend: Ticket Detail + Comments
Channel: #agent-coder
Task: Create TicketDetail.jsx showing ticket info, public comment thread (exclude is_internal), and form to post a new comment.
Commit: feat(frontend): add ticket detail page with comment thread

---

## [QUEUED] P-13 — SLA Policy CRUD (Should-Tier)
Channel: #agent-coder
Task: Create SLAPolicyController with index() and store(). Routes: GET/POST /api/sla-policies under sanctum. Scoped to auth user org.
Commit: feat(sla): add SLA policy endpoints

---

## [QUEUED] P-14 — Final Commit + SUBMISSION.md
Channel: #sprint-main
Task: git add -A, commit chore: final sprint-1 checkpoint. Update SUBMISSION.md with routes, data model, known limitations. Tag: git tag sprint-1-complete.
Commit: chore: final sprint-1 checkpoint and submission update

---

## COMMIT CONVENTION

| Prefix         | Use for                                  |
|----------------|------------------------------------------|
| feat(scope):   | New feature or endpoint                  |
| fix(scope):    | Bug fix                                  |
| chore:         | Config, tooling, CI                      |
| refactor(scope): | Restructure without behaviour change   |
| docs:          | Documentation only                       |

Scopes: db, auth, tickets, comments, frontend, sla, seed, activity, validation
