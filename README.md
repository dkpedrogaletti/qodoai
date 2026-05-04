# Qodo PR-Agent demo — PHP API + Next.js

Internal showcase for [**Qodo**](https://www.qodo.ai/) (formerly Codium): automated, high-signal **pull request** description, review, and improvement suggestions so teams spend review time on architecture and product decisions instead of repetitive checks.

This repo intentionally mirrors a common company layout:

- **`backend/`** — small PHP 8 HTTP API (`strict_types`, parameterized SQL)
- **`frontend/`** — Next.js App Router sample that reads `NEXT_PUBLIC_API_BASE_URL`

### Demo endpoint behavior

- `GET /users?prefix=Al&limit=3&fields=public` returns masked emails and pagination metadata.
- `fields=full` requires `X-Demo-Token` to match `DEMO_ADMIN_TOKEN`; otherwise API returns `403`.
- This intentionally creates a review surface around auth boundaries and sensitive-data exposure.

## Enable Qodo GitHub App (hosted mode)

1. **Push this repo** to GitHub (or fork it inside your org).
2. Install/configure the **Qodo Code Review** GitHub App and grant this repository access.
3. Open a **pull request**. Qodo should publish:
   - a **description** walkthrough (**`/describe`**),
   - a **structured review** (**`/review`**),
   - actionable **code suggestions** (**`/improve`**),

   Comments on the PR (`/review`, `/ask`, `/update_changelog`, etc.) trigger App actions.

Configuration in **`.pr_agent.toml`** biases reviews toward PHP API safety and Next.js App Router pitfalls (env exposure, RSC boundaries, caching).

### Hosted App vs self-hosted workflow

This repository is configured for **hosted Qodo App mode** (no repository LLM API keys required).  
If you later want self-hosted behavior, re-add a GitHub Actions workflow and provider secrets.

## Local run (optional, for demos in the IDE)

**PHP API** (from `backend/` — requires SQLite and the `pdo_sqlite` extension):

```bash
cd backend/public
php -S 127.0.0.1:8080
```

**Frontend:**

```bash
cd frontend
cp .env.example .env.local
npm install
npm run dev
```

## Suggested stakeholder demo script

1. Show a **baseline PR** (small refactor) → point at the PR summary and review sections.
2. Open a PR that **introduces a deliberate smell** (e.g. raw SQL concatenation in PHP or a sensitive value under `NEXT_PUBLIC_`) → show how automated review narrows debate to facts.
3. Mention **living rules**: encoding PHP/Next conventions in `.pr_agent.toml` + Org policies on Qodo for governance at scale.

## Links

- [Qodo platform](https://www.qodo.ai/)
- [PR-Agent (installation & usage)](https://github.com/qodo-ai/pr-agent)
