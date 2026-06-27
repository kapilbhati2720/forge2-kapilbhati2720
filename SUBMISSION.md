# Submission Details — PulseDesk

## 1. Repository Info
- **Repository URL:** https://github.com/kapilbhati2720/forge2-kapilbhati2720
- **First Commit SHA:** d2eb2a3ba2c3e9b81bc33c8dd9aad56f1540e6e7
- **Last Commit SHA:** dd5a5ed65ccb152e4c486387f977862e11c9fb9b

## 2. Project Loom Demo
- **Video URL (<= 5 min):** [Self-recorded or pending]

## 3. Slack Communication Verification
Exported screenshots showing the human-in-the-loop validation, Hermes-to-OpenClaw planning execution, and test passes are saved under:
- `slack-export/screenshots/`

## 4. Run instructions against surprise dataset
1. Fresh clone:
   ```bash
    git clone https://github.com/kapilbhati2720/forge2-kapilbhati2720
    cd forge2-kapilbhati2720
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
