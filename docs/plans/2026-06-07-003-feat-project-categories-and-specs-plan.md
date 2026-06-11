---
title: feat/project-categories-and-specs
created: 2026-06-07
status: active
---

# Plan: Category Master & Dynamic Project Specifications

This plan defines the steps to implement a master categorization system and dynamic specifications (specs) for PT. Sistem Jaya Abadi (SJA) construction projects in the CMS and public case study views.

---

## 1. Problem Frame & Scope

### Problem
* The public project details page renders mockup/static specifications like `Category: Luxury Private Villa` and `Execution Team: SJA Bali Engineering Unit`.
* The `projects` database table only has location, status, and description. It lacks structured fields for professional construction specifications (e.g. Client, Year of Completion, Land Area, Building Area, Execution Team, and Category).
* There is no administrative dashboard CRUD for categories.

### Scope Boundary
* **In Scope:**
  * **Database & Schema**: Create a `categories` table, define a relationship between `projects` and `categories`, and add specs columns (`client`, `year`, `building_area`, `land_area`, `execution_team`) to the `projects` table.
  * **Category CRUD**: Build a complete category administration interface (index, create, edit, delete) matching SJA styles.
  * **Project CRUD Integration**: Modify project forms to include a Category dropdown selection and text inputs for all construction specs.
  * **Public Case Study Update**: Render specs dynamically on the public project page.
  * **Seeder**: Seed initial categories (e.g., Residential, Commercial, Villa, Infrastructure) and link them to default projects.
* **Out of Scope**:
  * Modifying unrelated layouts, headers, or features.

---

## 2. Key Technical Decisions

### Database Schema
* **Categories Table (`categories`)**:
  * `id` (primary key)
  * `name` (string) - e.g. "Residential Construction"
  * `slug` (string, unique) - e.g. "residential-construction"
  * Timestamps
* **Projects Table (`projects`) Additions**:
  * `category_id` (foreign key to `categories`, nullable to support existing seeders, but constrained).
  * `client` (string, nullable) - e.g. "Private Client"
  * `year` (string, nullable) - e.g. "2025"
  * `building_area` (string, nullable) - e.g. "450 sqm"
  * `land_area` (string, nullable) - e.g. "800 sqm"
  * `execution_team` (string, nullable) - e.g. "SJA Bali Engineering Unit"

### Relationships (Eloquent)
* `Category` model: `hasMany(Project::class)`
* `Project` model: `belongsTo(Category::class)` (with Laravel 13 style attribute fillable lists).

### SJA Layout Guidelines
* Category administration views will extend `@extends('layouts.vertical', ['title' => 'Categories'])` and match the card-based list/form patterns used in Projects.
* All admin forms and buttons must use English.

---

## 3. Implementation Units

### U1. Database Migrations
* Create migration for `categories` table.
* Create migration to add specs and `category_id` fields to `projects` table.
* Update `DatabaseSeeder.php` to seed categories and link them.

### U2. Eloquent Models Update
* Create `Category` model using Laravel 13 features.
* Update `Project` model: add `category_id` and specs fields to `Fillable` attribute and define `category()` relationship.

### U3. Category CRUD Interface (Admin)
* Create `CategoryController` (Resource controller).
* Register `Route::resource('categories', CategoryController::class)` inside `routes/web.php`.
* Design views:
  * `resources/views/categories/index.blade.php` (Card list, actions)
  * `resources/views/categories/create.blade.php` (Form)
  * `resources/views/categories/edit.blade.php` (Form)
* Add a "Categories" menu link in Sidenav `layouts/partials/sidenav.blade.php`.

### U4. Project CRUD Updates (Admin)
* Update `StoreProjectRequest` and `UpdateProjectRequest` validation rules.
* Modify Project Controller to pass categories list to `create` and `edit` views.
* Update create.blade.php and edit.blade.php:
  * Add a select dropdown for Category.
  * Add grid inputs for Client, Year, Building Area, Land Area, and Execution Team.
* Update Project show view show.blade.php to display these specs.

### U5. Public Project Case Study details Update
* Edit public case study view show.blade.php.
* Make the Project Specs sidebar fully dynamic using the new project specs model attributes.

---

## 4. Verification

* **Automated Tests**:
  * Write feature test assertions verifying project updates and category relations work correctly.
* **Visual QA**:
  * Use `agent-browser` to register a new category, assign it to a project, edit specifications, and verify the public case study page renders the details correctly.
