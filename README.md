# PulseDesk

A multi-tenant customer support ticket platform (Zendesk-like). Built for the Forge2 Edition 1 Hackathon.

## Tech Stack
- **Backend:** PHP 8.2+ / Laravel 11 / MySQL 8
- **Frontend:** React 19 + Vite / TailwindCSS (optional, styled with premium vanilla CSS by default)
- **Auth:** Laravel Sanctum
- **Tests:** Pest / PHPUnit

## Agent Roles
- **Hermes (@hermes):** Orchestrator & Planner. Responsible for scoping, sprint planning, task decomposition, and tracking progress. Active model: `z-ai/glm-5.1` (via EastRouter).
- **OpenClaw (@OpenClaw):** Executor & Coder. Writes code, executes migrations, runs tests, and reports results to `#agent-coder`. Active model: `eastrouter/moonshotai/kimi-k2.7-code`.

## Workspace Communication Layout
All bot communication occurs exclusively in Slack channels:
- `#sprint-main` — Main coordinate and task assignment
- `#agent-orchestrator` — Hermes plans & statuses
- `#agent-coder` — OpenClaw reports and task updates
- `#ci-cd` — Test & build results
- `#human-review` — Human approval gate for pull requests/merges

## Setup Instructions

### Backend Setup
1. Navigate to the backend directory:
   ```bash
   cd backend
   ```
2. Install dependencies:
   ```bash
   composer install
   ```
3. Copy environment file and configure DB settings:
   ```bash
   cp .env.example .env
   ```
4. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```
5. Start local server:
   ```bash
   php artisan serve
   ```

### Frontend Setup
1. Navigate to the frontend directory:
   ```bash
   cd frontend
   ```
2. Install dependencies:
   ```bash
   npm install
   ```
3. Run the development server:
   ```bash
   npm run dev
   ```
