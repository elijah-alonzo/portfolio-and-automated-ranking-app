# PSG E-Portfolio and Ranking System

Laravel 12, PHP 8.2+, Filament 4 admin at `/ranking`, public landing page in Inertia + React (JavaScript), Tailwind 4, Pest.

## Map

- `app/Features/<Name>/` domain code: `Models`, `Enums`, `Actions`, `Controllers`. Filament stays in `app/Filament/` and is not nested by feature.
- `resources/js/` landing page. Pages in `pages/`, sections in `components/sections/`, shared pieces in `components/shared/`, static copy in `data/`.
- `routes/web.php` public and evaluation routes.

## Commands

- Setup: `composer setup`. Dev: `composer dev`. Test: `composer test` (pint, phpstan, Pest).
- Format: `composer lint`. Build front end: `npm run build`.

## Rules

- Scoring lives in `Features/Evaluations/Actions` (`CalculateFinalScore`, `UpdateEvaluationRank`) with weights on `EvaluatorType` and tiers on `RankTier`. Never put score math in models or Filament pages.
- Actions are `final` and invokable, and return data, never HTTP responses.
- Change behavior, add or update a Pest test.
- Do not commit or push unless asked. Commit style: `Feat | ...`, `Fix | ...`, `Refactor | ...`, `Docs | ...`.
