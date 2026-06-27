# Architecture Design — PulseDesk

PulseDesk is a multi-tenant support ticket system where organizations manage their users, agents, and support tickets in complete isolation.

## 1. Multi-Tenancy Strategy
- **Isolation Level:** Database-level logical isolation (Shared Database, Shared Schema).
- **Tenant Scope:** Every tenant is represented by an `organizations` record.
- **Scoping Key:** The `organization_id` foreign key is required on every data table (e.g., `users`, `tickets`, `replies`).
- **Enforcement:** Global Query Scopes in Laravel models (e.g., a custom `TenantScope` class or `belongsTo(Organization::class)` relationship checking) to ensure Org A can never see or modify Org B's data under any API request.

## 2. Database Schema Design (Actual)

### `organizations`
- `id` (bigint, PK)
- `name` (varchar)
- `slug` (varchar, unique)
- `timestamps`

### `users`
- `id` (bigint, PK)
- `organization_id` (bigint, FK)
- `name` (varchar)
- `email` (varchar, unique within organization)
- `password` (varchar)
- `role` (enum: 'admin', 'agent', 'customer')
- `timestamps`

### `tickets`
- `id` (bigint, PK)
- `organization_id` (bigint, FK)
- `subject` (varchar)
- `description` (text, nullable)
- `status` (enum: 'open', 'pending', 'on_hold', 'resolved', 'closed')
- `priority` (enum: 'low', 'medium', 'high', 'urgent')
- `requester_id` (bigint, FK to `users` - customer who raised it)
- `assignee_id` (bigint, FK to `users` - agent assigned, nullable)
- `resolved_at` (timestamp, nullable)
- `first_response_at` (timestamp, nullable)
- `timestamps`
- `deleted_at` (softDeletes)

### `comments`
- `id` (bigint, PK)
- `organization_id` (bigint, FK)
- `ticket_id` (bigint, FK)
- `author_id` (bigint, FK to `users`)
- `body` (text)
- `is_internal` (boolean - true if agents-only note, false if customer-visible reply)
- `timestamps`

### `sla_policies`
- `id` (bigint, PK)
- `organization_id` (bigint, FK, unique with priority)
- `name` (varchar)
- `priority` (enum: 'low', 'medium', 'high', 'urgent')
- `response_minutes` (unsignedInteger)
- `resolution_minutes` (unsignedInteger)
- `is_default` (boolean)
- `timestamps`

### `activity_logs`
- `id` (bigint, PK)
- `organization_id` (bigint, FK)
- `ticket_id` (bigint, FK)
- `user_id` (bigint, FK to `users`)
- `type` (varchar - e.g. priority_changed, status_changed, assignee_changed)
- `meta` (json - stores from/to changes)
- `timestamps`

---

## 3. API Routes Layout
All API endpoints are protected by Laravel Sanctum:

- `POST /api/register` — Register a new organization and admin user
- `POST /api/login` — Login to receive a Sanctum token
- `GET /api/tickets` — List tickets scoped by active user's tenant organization (with status/priority filters)
- `POST /api/tickets` — Create a ticket
- `GET /api/tickets/{id}` — Fetch ticket detail & comment thread
- `PUT /api/tickets/{id}` — Update ticket (status, assignee, priority)
- `POST /api/tickets/{id}/comments` — Add a new public comment or internal note
- `GET /api/sla-policies` — List SLA policies for tenant
- `POST /api/sla-policies` — Create SLA policy
- `GET /api/sla-policies/{id}` — View SLA policy detail
- `PUT /api/sla-policies/{id}` — Update SLA policy
- `DELETE /api/sla-policies/{id}` — Delete SLA policy
- `GET /api/stats` — Fetch dashboard metrics (ticket counts, average resolution time, SLA breach rates)

---

## 4. Model Routing & Execution Flow
```mermaid
graph TD
    User([User in Slack]) -->|Mentions| Hermes[Hermes - Planner z-ai/glm-5.1]
    Hermes -->|Decomposes Task & Writes Plan| MainSlack[#sprint-main]
    Hermes -->|Assigns task| OpenClaw[OpenClaw - Executor kimi-k2.7-code]
    OpenClaw -->|Performs Local Actions| Codebase[(Local Laravel/React Codebase)]
    OpenClaw -->|Runs Tests & Lints| Tests[Pest/PHPUnit Suite]
    OpenClaw -->|Reports status/PR links| CoderChannel[#agent-coder]
```
- **Planning (Hermes):** Evaluates complexity, coordinates sprints, designs schemas, and ensures multi-tenant policies are correctly identified before assigning files to be modified.
- **Execution (OpenClaw):** Focuses on low-level coding task speed, follows Laravel best practices, writes tests, and runs validations.
