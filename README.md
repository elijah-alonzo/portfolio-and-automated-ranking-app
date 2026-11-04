<div align="center">
    
![logo](public/sys-logo.png)

</div>

<p> A capstone project developed to automate student evaluation, score tallying, and ranking, replacing manual processes with an online evaluation platform. The system allows students to apply for leadership awards, complete evaluations, and provides the council with tools to manage evaluations, automate performance-based rankings, and issue digital certificates. </p>

![grading-sys](docs/screenshots/landing-page.png)

## Tech Stack

- Laravel 12 and PHP 8.2+
- Filament 4 for the admin and evaluation panel
- Inertia.js with React 19 and Vite for the public landing page
- Tailwind CSS 4, Pest, Larastan, Pint
- SQLite, Docker and GitHub Actions

## Architecture

Domain code lives in `app/Features/<Name>/` (models, enums, actions, controllers). Scoring is isolated in invokable actions so it can be tested without the UI:

- `CalculateFinalScore` weights adviser 65%, peer 25% and self 10%, and returns `null` until all three exist.
- `RankTier` maps the final score to Gold (2.41+), Silver (1.81+) or Bronze (1.21+).
- `UpdateEvaluationRank` ties them together when a form is submitted.

## Getting Started

Needs PHP 8.2+ with the `sqlite3` extension, Composer and Node 24. No database server required.

```bash
composer setup        # install, .env, key, SQLite file, migrate and seed, build assets
composer dev          # http://localhost:8000
```

Sign in at `/ranking/login` with a seeded account such as `admin@psg.com` / `password`. Other seeded logins: `siteadviser@psg.com`, `psgstudent1@psg.com` to `psgstudent4@psg.com`, `sitestudent1@psg.com` to `sitestudent4@psg.com`.

### Docker

```bash
cp .env.example .env
docker compose up --build   # http://localhost:8000, SQLite lives in a volume
```

### Checks

```bash
composer test         # pint, phpstan, Pest
```

## Features

### Council Evaluation & Management

The Council Evaluation and Management section provides the council with access to review and manage student evaluations. It promotes transparency throughout the evaluation process while giving authorized council members a centralized way to monitor submissions and evaluation results.

![grading-sys](docs/screenshots/council-eval-view.png)

### Online Evaluation

The Online Evaluation section allows students to complete their required evaluation forms digitally. This replaces manual evaluation forms with an online process, making it easier to collect responses and organize evaluation data for scoring and ranking.

![grading-sys](docs/screenshots/my-eval-eval.png)

### Leadership Award Application

The Leadership Award Application allows students to submit their applications for leadership awards online. It replaces manual application processes with a centralized digital platform, making submissions easier to manage and reducing the administrative effort required to collect applications.

![grading-sys](docs/screenshots/portfolio-apply.png)