# Submission Details — PulseDesk

## 1. Repository Info
- **Repository URL:** [Pending]
- **First Commit SHA:** [Pending]
- **Last Commit SHA:** [Pending]

## 2. Project Loom Demo
- **Video URL (<= 5 min):** [Pending]

## 3. Slack Communication Verification
Exported screenshots showing the human-in-the-loop validation, Hermes-to-OpenClaw planning execution, and test passes are saved under:
- `slack-export/screenshots/`

## 4. Run instructions against surprise dataset
1. Fresh clone:
   ```bash
   git clone <repo-url>
   cd forge2-<name>
   ```
2. Run database migration and seeds:
   ```bash
   cd backend
   php artisan migrate:fresh --seed
   ```
3. Run tests:
   ```bash
   php artisan test
   ```
