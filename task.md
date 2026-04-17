# Evaluator Assignment Bug Fix — Council Evaluations

## Context

This system manages **Council Evaluations** created by admins and assigned to advisers. Each council has enrolled students who undergo three types of evaluation:

1. **Adviser evaluation** — the assigned adviser evaluates each student
2. **Peer evaluation** — students evaluate fellow students (manually assigned by the adviser, no limits)
3. **Self evaluation** — each student evaluates themselves

The evaluation scores from all three criteria are computed together by the existing system to produce a final score and rank. **Do not touch the scoring/computation logic.** The fix is scoped entirely to evaluator assignment.

---

## Current Schema (Buggy)

All three evaluation types store `evaluator_id` directly on the evaluation row:

```
adviser_evaluations
  - id
  - council_evaluation_id (FK → council_evaluations)
  - evaluatee_id (FK → users)
  - evaluator_id (FK → users)   ← BUG: redundant, diverges from council adviser
  - scores (json)
  - remarks
  - status

peer_evaluations
  - id
  - council_evaluation_id (FK → council_evaluations)
  - evaluatee_id (FK → users)
  - evaluator_id (FK → users)   ← BUG: no assignment tracking, allows duplicates
  - scores (json)
  - remarks
  - status

self_evaluations
  - id
  - council_evaluation_id (FK → council_evaluations)
  - evaluatee_id (FK → users)
  - evaluator_id (FK → users)   ← redundant: evaluatee IS the evaluator
  - scores (json)
  - remarks
  - status
```

---

## The Two Bugs

### Bug 1 — Adviser auto-assignment is unreliable

`adviser_evaluations.evaluator_id` is a separate field that must be manually set to the adviser's ID at creation time. The adviser is already stored in `council_evaluations.adviser_id`, so this is duplicate data that can fall out of sync — for example, if the adviser is reassigned after evaluation rows are created, or if the assignment logic runs at the wrong time.

### Bug 2 — Peer evaluator assignments get duplicated or lost

Peer evaluator assignment is managed by writing directly to `peer_evaluations` rows. There is no separate tracking of _who is supposed to evaluate whom_, so:

- Duplicate rows can be inserted for the same (evaluatee, evaluator) pair
- Reassigning or removing a peer evaluator is unclear — which row do you delete?
- There is no clean way to distinguish "assigned but not yet submitted" from "submitted"

---

## Recommended Fix

### 1. Remove `evaluator_id` from `adviser_evaluations`

The evaluator is always `council_evaluations.adviser_id`. Remove the redundant column and update all queries that reference `adviser_evaluations.evaluator_id` to join through `council_evaluations` instead:

```sql
SELECT ce.adviser_id AS evaluator_id
FROM adviser_evaluations ae
JOIN council_evaluations ce ON ae.council_evaluation_id = ce.id
WHERE ae.id = :id;
```

When creating `adviser_evaluations` rows (i.e. when a student is added to a council), no evaluator field needs to be set — it's always derived from the council.

### 2. Remove `evaluator_id` from `self_evaluations`

The evaluator is always the evaluatee. Remove the column. When creating self-evaluation rows, no evaluator field is needed — the evaluatee is the evaluator by definition.

### 3. Introduce `peer_evaluation_assignments` table

Create a new table to serve as the source of truth for peer evaluator assignments:

```sql
CREATE TABLE peer_evaluation_assignments (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  council_evaluation_id UUID NOT NULL REFERENCES council_evaluations(id),
  evaluatee_id UUID NOT NULL REFERENCES users(id),
  evaluator_id UUID NOT NULL REFERENCES users(id),
  assigned_at TIMESTAMP DEFAULT NOW(),
  UNIQUE (council_evaluation_id, evaluatee_id, evaluator_id)
);
```

The `UNIQUE` constraint on `(council_evaluation_id, evaluatee_id, evaluator_id)` prevents duplicate assignments at the database level.

### 4. Link `peer_evaluations` to an assignment

Update `peer_evaluations` to reference the assignment instead of storing the evaluator directly:

```sql
-- Remove evaluator_id column
-- Add assignment_id column

ALTER TABLE peer_evaluations
  DROP COLUMN evaluator_id,
  DROP COLUMN council_evaluation_id,
  ADD COLUMN assignment_id UUID NOT NULL REFERENCES peer_evaluation_assignments(id) UNIQUE;
```

The `UNIQUE` on `assignment_id` ensures one submission per assignment. An evaluation row can only exist if a valid assignment exists — this eliminates orphaned or duplicated evaluations.

---

## Updated Flow

### When a student is added to a council

- Insert into `council_members`
- Insert an `adviser_evaluation` row (no evaluator field needed)
- Insert a `self_evaluation` row (no evaluator field needed)

### When an adviser assigns a peer evaluator

- Insert into `peer_evaluation_assignments (council_evaluation_id, evaluatee_id, evaluator_id)`
- The DB unique constraint handles duplicate prevention automatically

### When a peer submits their evaluation

- Insert into `peer_evaluations (assignment_id, scores, remarks, status)`

### When fetching the evaluator for any evaluation type

| Type    | How to get evaluator                               |
| ------- | -------------------------------------------------- |
| Adviser | `JOIN council_evaluations ON adviser_id`           |
| Peer    | `JOIN peer_evaluation_assignments ON evaluator_id` |
| Self    | `evaluatee_id` on the self_evaluation row          |

---

## What NOT to Change

- The scoring and computation logic that derives the final score and rank from the three evaluation types — this is out of scope and should not be touched.
- The `council_evaluations`, `council_members`, and `users` table structures — these are fine as-is.
