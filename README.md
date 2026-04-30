# Student Council Ranking System

![Ranking System](public/ranking-sys.png)

An e-portfolio and ranking system for the Paulinian Student Government built with Laravel 11 and Filament v4. This system manages council evaluations, peer assessments, portfolio management, and leadership award applications.

## System Overview

This application provides systematic evaluation of student council members through:

- **Multi-tiered evaluation process** (Self, Peer, Adviser evaluations)
- **Portfolio management** for student achievements and certificates
- **Automated ranking calculations** based on weighted evaluation scores
- **Leadership award applications** for graduating students
- **Administrative dashboard** for managing councils, users, and evaluations
- **Role-based access control** (Admin, Adviser, Student)

## Key Features

### For Administrators

- **User Management**: Create and manage admin, adviser, and student accounts
- **Council Management**: Setup and configure student councils with award types
- **Evaluation Management**: Create evaluation sessions and monitor progress
- **Award Type Management**: Define various leadership award categories
- **Award Applications**: Review and process student award applications
- **System-wide Analytics**: Monitor evaluations, users, and council activities

### For Advisers

- **My Evaluations**: Access assigned council evaluations
- **Student Management**: Add students to evaluations and assign positions
- **Peer Evaluator Assignment**: Assign peer evaluators to students
- **Evaluation Forms**: Complete adviser evaluations for students
- **Progress Tracking**: Monitor evaluation submission status

### For Students

- **My Evaluations**: View assigned evaluations and complete forms
- **Self-Evaluation**: Assess own performance and contributions
- **Peer Evaluation**: Evaluate assigned peers (when designated)
- **My Portfolio**: View achievements, certificates, and evaluation history
- **Certificate Management**: Upload and manage achievement certificates
- **Award Applications**: Apply for leadership awards (for graduating students)

## System Functionalities

The following walkthrough mirrors the in-app Help pages and highlights the main workflows with screenshots.

### Dashboard and Profile

- **Dashboard Overview**: Quick view of key activities and shortcuts.

![Dashboard Overview](public/assets/dashboard.png)

### My Councils (Evaluations)

- **Council List**: View councils you participate in or manage.

![My Councils List](public/assets/my-eval/list.png)

- **Council Evaluation Flow**: Manage evaluation phases (Closed, Open, Completed).

![Council Evaluation View](public/assets/my-eval/view.png)

- **Evaluation Form**: Submit self, peer, or adviser evaluations during Open phase.

![Evaluation Form](public/assets/my-eval/eval.png)

### Portfolio

- **Portfolio Overview**: View leadership and extracurricular achievements.

![Portfolio Overview](public/assets/portfolio/view.png)

- **Apply for Leadership Awards**: Select award type and confirm graduation status.

![Apply for Awards](public/assets/portfolio/apply.png)

- **Issued Certificates**: Review and download issued certificates.

![Issued Certificates](public/assets/portfolio/cert.png)

### User Management (Admin)

- **Users Table**: Manage student, adviser, and admin accounts.

![Users Table](public/assets/users/list.png)

- **User Form**: Create or update user profiles and roles.

![User Form](public/assets/users/form.png)

### Departments (Admin)

- **Departments List**: Organize users and councils by department.

![Departments List](public/assets/departments/list.png)

- **Department Form**: Create or update department details.

![Department Form](public/assets/departments/form.png)

### Councils (Admin)

- **Councils List**: Manage student councils and their details.

![Councils List](public/assets/councils/list.png)

- **Council Form**: Define council settings, departments, and award types.

![Council Form](public/assets/councils/form.png)

### Council Evaluations (Admin)

- **Evaluations List**: Track evaluation sessions per council.

![Evaluations List](public/assets/council-eval/list.png)

- **Evaluation Details**: Monitor scores, ranks, and recommendations.

![Evaluation Details](public/assets/council-eval/view.png)

- **Evaluation Forms Review**: Access submitted evaluation forms.

![Evaluation Forms Review](public/assets/council-eval/eval.png)

- **Create Evaluation**: Configure council, adviser, and academic year.

![Create Evaluation](public/assets/council-eval/form.png)

### Council Positions (Admin)

- **Positions List**: Define student officer roles.

![Positions List](public/assets/positions/list.png)

- **Position Form**: Create or update position configuration.

![Position Form](public/assets/positions/form.png)

### Certificates (Admin/Adviser)

- **Certificates List**: Track issued certificates per student.

![Certificates List](public/assets/certs/list.png)

- **Issue Certificate**: Upload certificate and assign recipients.

![Issue Certificate](public/assets/certs/form.png)

### Award Types (Admin)

- **Award Types List**: Manage leadership award categories.

![Award Types List](public/assets/awards/list.png)

- **Award Type Form**: Create or update award type details.

![Award Type Form](public/assets/awards/form.png)

### Award Applications (Admin)

- **Applications List**: Review award applications and student ranks.

![Applications List](public/assets/applications/list.png)

## Project Architecture

```
Student Council Ranking System
├── app/
│   ├── Models/
│   │   ├── User.php                         # User accounts (Admin/Adviser/Student)
│   │   ├── Council.php                      # Student councils/organizations
│   │   ├── AwardType.php                    # Award type definitions
│   │   ├── Evaluation.php                   # Evaluation sessions
│   │   ├── EvaluationForm.php               # Form responses & scoring engine
│   │   ├── EvaluationPeerEvaluator.php      # Peer evaluation assignments
│   │   ├── EvaluationRank.php               # Final ranking calculations
│   │   ├── Certificate.php                  # Achievement certificates
│   │   └── LeadershipAwardApplication.php   # Award applications
│   │
│   ├── Http/
│   │   └── Controllers/
│   │       └── EvaluationSubmissionController.php # Evaluation submission logic
│   │
│   ├── Filament/
│   │   ├── Resources/
│   │   │   ├── Users/                       # User management (Admin)
│   │   │   ├── Councils/                    # Council management (Admin)
│   │   │   ├── AwardTypes/                  # Award type management (Admin)
│   │   │   ├── Evaluations/                 # Evaluation setup (Admin)
│   │   │   ├── LeadershipAwardApplications/ # Award applications (Admin)
│   │   │   ├── MyEvaluations/               # Evaluator interface (Adviser/Student)
│   │   │   ├── MyPortfolio/                 # Student portfolios (Student)
│   │   │   └── Certificates/                # Certificate management (Student)
│   │   │
│   │   ├── Pages/
│   │   │   └── Auth/
│   │   │       └── EditProfile.php          # User profile management
│   │   │
│   │   └── Widgets/
│   │       ├── AccountWidget.php            # Account information widget
│   │       ├── StatsOverview.php            # Student/Adviser statistics
│   │       └── AdminStatsOverview.php       # Admin dashboard statistics
│   │
│   └── Providers/
│       └── Filament/
│           └── RankingPanelProvider.php     # Filament panel configuration
│
├── resources/
│   └── views/
│       ├── EvaluationForm/                  # Custom evaluation interfaces
│       │   ├── AdviserEvaluation.blade.php
│       │   ├── PeerEvaluation.blade.php
│       │   └── SelfEvaluation.blade.php
│       └── Portfolio/
│           └── PortfolioView.blade.php      # Portfolio display template
│
└── database/
    └── migrations/                          # Database schema definitions
```

## Core Evaluation Workflow

1. **Admin Creates Evaluation**
    - Selects council and adviser
    - Sets academic year
    - Evaluation is created in "pending" status

2. **Adviser Manages Students**
    - Adds student officers to evaluation
    - Assigns positions to each student
    - Assigns peer evaluators (1-to-1 mapping)

3. **Evaluators Complete Forms**
    - **Self**: Students evaluate themselves
    - **Peer**: Designated peers evaluate assigned students
    - **Adviser**: Council adviser evaluates all students

4. **Automatic Ranking**
    - System calculates weighted scores
    - Generates final rankings
    - Displays scores in student portfolios

5. **Award Applications**
    - Graduating students apply for leadership awards
    - Admin reviews applications and portfolios
    - Awards are accepted/rejected

## Database Schema Highlights

### Core Tables

- `users` - User accounts with roles (admin/adviser/student)
- `councils` - Student councils with award type associations
- `award_types` - Types of leadership awards
- `evaluations` - Evaluation sessions
- `evaluation_user` - Student participation in evaluations (pivot)
- `evaluation_peer_evaluators` - Peer evaluator assignments
- `evaluation_forms` - Evaluation responses and scores
- `evaluation_ranks` - Calculated rankings
- `certificates` - Student achievement certificates
- `leadership_award_applications` - Award applications

### Key Relationships

- Council → Evaluation (One-to-Many)
- Evaluation → Users (Many-to-Many via pivot)
- Evaluation → EvaluationForms (One-to-Many)
- User → Certificates (One-to-Many)
- User → LeadershipAwardApplications (One-to-Many)

## Technology Stack

- **Framework**: Laravel 11
- **Admin Panel**: Filament v4
- **Database**: MySQL
- **Frontend**: Blade Templates, Tailwind CSS (via Filament)
- **Authentication**: Laravel Breeze (Filament integration)
- **File Storage**: Laravel Storage (for certificates and profile pictures)

## Installation

1. **Clone the repository**

    ```bash
    git clone <repository-url>
    cd ranking-sys
    ```

2. **Install dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Configure environment**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Setup database**
    - Create database
    - Update `.env` with database credentials

    ```bash
    php artisan migrate
    ```

5. **Create storage link**

    ```bash
    php artisan storage:link
    ```

6. **Run development server**

    ```bash
    php artisan serve
    npm run dev
    ```

7. **Access the system**
    - Navigate to `http://localhost:8000/ranking`
    - Login with your credentials
