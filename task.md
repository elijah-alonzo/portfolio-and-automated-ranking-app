# Feature Specification: Leadership Award Application + Award Types + Council Updates

## Project Context
- Framework: **Laravel 11**
- Admin Panel: **Filament 4**
- Environment: **NOT in production**  
  - ✅ It is safe to modify and update existing migration files.
  - ✅ Foreign keys and schema changes can be added directly.

---

# 1. Leadership Award Application Feature

## Student-Side

### Button Placement
- Add a **"Apply for Leadership Award"** button on the **Portfolio index/view page**.

### Application Form Fields
When clicked, the student fills out:

- **Award Type** (Select dropdown)
  - Comes from `award_types` table.
- **Graduating Confirmation** (Required checkbox)
  - Example label:
    > "I confirm that I am graduating."
  - Required validation.
  - Does NOT need to be stored in the database.

### Submission Behavior
- Create a new `leadership_award_applications` record.
- Default status: `pending`.

---

# 2. Database Structure

## A. leadership_award_applications Table

Fields:

- `id`
- `user_id` (FK → users table)
- `award_type_id` (FK → award_types table)
- `status` (enum: `pending`, `accepted`, `rejected`)
- `created_at`
- `updated_at`

Default:
- `status = pending`

---

## B. Award Types Table (NEW)

Create a new table:

### award_types

Fields:

- `id`
- `name` (string)
- `description` (text, nullable)
- `created_at`
- `updated_at`

---

# 3. New Filament Resource: AwardTypeResource

Create a Filament resource:

## AwardTypeResource

Admins should be able to:

- Create award types
- Edit award types
- Delete award types

### Form Fields:
- `name` (required)
- `description` (textarea, optional)

### Table Columns:
- Name
- Description
- Created At

---

# 4. Update Council Table

We need to modify the existing `councils` table.

## Add New Field

Add:

- `award_type_id` (foreign key → award_types table, nullable if needed)

Since the project is NOT in production:
- ✅ You may directly modify the original migration file.
- OR
- ✅ Create a new migration to add the foreign key.

Relationship:
- A council belongs to an award type.

---

# 5. Admin Panel: LeadershipAwardApplicationResource

Create:

## LeadershipAwardApplicationResource

### Table Columns

Admin should see:

- Student Name (relationship → user)
- Award Type (relationship → awardType)
- Status
- Created At
- Portfolio Link (admin view of student's portfolio)

### Status Editing

- Status should be editable inline in the table (SelectColumn if possible).
- Allowed values:
  - `pending`
  - `accepted`
  - `rejected`

### Tabs (Filtering by Status)

Add tabs at the top:

- All
- Pending
- Accepted
- Rejected

Each tab filters by `status`.

---

# 6. Admin Portfolio View Update

In the **Admin Portfolio View**, inside the:

## Council Experience Section

Display:

- Council Position
- Rank Result
- Award Type (from award_types relationship)

So the council experience should now show:

- Council Name
- Position
- Rank Result
- Award Type Name

Make sure:
- The council model has a `belongsTo(AwardType::class)`
- The admin portfolio view loads the relationship properly.

---

# 7. Relationships Overview

## Models & Relationships

### User
- hasMany LeadershipAwardApplications

### LeadershipAwardApplication
- belongsTo User
- belongsTo AwardType

### AwardType
- hasMany LeadershipAwardApplications
- hasMany Councils

### Council
- belongsTo AwardType

---

# Final System Overview

## Student Flow
1. Student opens Portfolio.
2. Clicks "Apply for Leadership Award".
3. Selects Award Type.
4. Confirms graduating.
5. Application saved as `pending`.

## Admin Flow
1. Admin manages Award Types.
2. Admin views Leadership Award Applications.
3. Admin filters via tabs.
4. Admin changes status inline.
5. Admin views student portfolio.
6. In portfolio council section:
   - Rank Result is shown.
   - Award Type is shown.

---

# Important Notes

- The system is NOT in production.
- Migration files can safely be edited.
- Ensure all foreign keys use proper constraints.
- Use Filament relationships for select fields and table columns.