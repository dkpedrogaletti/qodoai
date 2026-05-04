# Qodo PR-Agent demo — PHP API + Next.js

Internal showcase for [**Qodo**](https://www.qodo.ai/) (formerly Codium): automated, high-signal **pull request** description, review, and improvement suggestions so teams spend review time on architecture and product decisions instead of repetitive checks.

This repo intentionally mirrors a common company layout:

- **`backend/`** — small PHP 8 HTTP API (`strict_types`, parameterized SQL)
- **`frontend/`** — Next.js App Router sample that reads `NEXT_PUBLIC_API_BASE_URL`

## Enable PR-Agent on GitHub

1. **Push this repo** to GitHub (or fork it inside your org).
2. Under **Settings → Secrets and variables → Actions**, create a repository secret:
   - **Name:** `OPENAI_KEY`  
   - **Value:** your provider key (same variable name as in the [official install guide](https://github.com/qodo-ai/pr-agent/blob/main/docs/docs/installation/github.md); you can also point PR-Agent at other models via workflow `env`).
3. Merge **`.github/workflows/pr_agent.yml`** to your default branch.
4. Open a **pull request**. On open / reopen / ready for review you should see the bot publish:
   - a **description** walkthrough (**`/describe`**),
   - a **structured review** (**`/review`**),
   - actionable **code suggestions** (**`/improve`**),

   Comments on the PR (`/review`, `/ask`, `/update_changelog`, etc.) tell the workflow to react to **issue comment** triggers.

Configuration in **`.pr_agent.toml`** biases reviews toward PHP API safety and Next.js App Router pitfalls (env exposure, RSC boundaries, caching).

### Managed Qodo vs self-hosted workflow

Teams often start with **Qodo Git integration / cloud** ([qodo.ai](https://www.qodo.ai/)) for less operational overhead than wiring keys in Actions. This repository uses the **open-source GitHub Action** so you own the demo wiring and can show it alongside the product narrative.

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
