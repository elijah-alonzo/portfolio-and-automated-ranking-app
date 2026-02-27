# AI Technical Specification: Portfolio Section Revamp

**Target Framework:** Laravel 11 / Filament v4  
**Objective:** Replace the standard Filament Resource View with a high-fidelity custom Blade-based portfolio and integrated PDF generation system.

---

## 1. Core Component: Custom View Implementation
* **Target View:** `/resources/views/Portfolio/Portfolio.blade.php`
* **Instruction:** Override the default Filament `ViewRecord` page. Do not use the standard Filament Schema; instead, return the custom Blade view while ensuring the `$record` instance is passed to the frontend.
* **Design Reference:** Mimic the structural CSS, typography, and grid layout found in `/resources/views/EvaluatonForm/AdviserEvaluationForm.blade.php`.
* **Visual Asset:** Use `portfolio.png` as the primary layout background/wrapper to ensure the digital version matches the intended physical print.

---

## 2. Header Actions & Navigation
The `ViewPortfolio` page must include a custom header action bar with the following:

### A. Profile Shortcut
* **Label:** `Edit Profile`
* **Icon:** `heroicon-o-pencil-square`
* **Function:** Direct redirect to the `EditRecord` page of the current Portfolio resource.

### B. PDF Generation & Print
* **Label:** `Download PDF`
* **Icon:** `heroicon-o-arrow-down-tray`
* **Method:** Implement a Filament `HeaderAction`.
* **Technical Requirement:** * Use a PDF dependency (e.g., **Spatie Laravel Browsershot** or **DomPDF**).
    * The action must render the `Portfolio.blade.php` view into a PDF format.
    * Ensure the background image (`portfolio.png`) and custom CSS styles are correctly injected into the PDF print stream.

---

## 3. Implementation Logic (For AI Reference)

### View Override (Filament Page)
 Base it on the logic used in renderring the AdviserEvaluationForm.blade.php