# Sprint Kickoff Skill
# Used by Hermes to structure sprint planning messages to OpenClaw

name: sprint-kickoff
description: >
  Formats a sprint goal into a structured task brief for OpenClaw.
  Includes codebase recon summary, issue list with acceptance criteria,
  branch naming convention, and commit/PR checklist.

## Usage

Trigger this skill when the human posts a new sprint goal in #sprint-main.

## Output Format

```
@OpenClaw Sprint {N} — {title}

Branch: feature/sprint-{N}-{slug}

Issues:
  {N}.1 — {issue title}
    Files: {exact file paths}
    AC: {acceptance criteria}

  {N}.2 — ...

Checklist:
  [ ] All tests pass (php artisan test)
  [ ] No secrets in committed files
  [ ] PR opened to main
  [ ] PR link posted in #human-review
  [ ] Status reported in #agent-log (What I Did / What's Left / What Needs Your Call)
```

## Recon Steps (run before drafting brief)

1. Read ARCHITECTURE.md for current schema
2. Run `php artisan route:list` to see existing endpoints
3. Check migrations for existing tables before adding new ones
4. Check existing model $fillable lists
