---
title: feat/sidebar-cleanup-and-dashboard
created: 2026-06-07
status: active
---

# Plan: Sidebar Cleanup & Custom Dashboard Implementation

This plan defines the process for cleaning up the redundant template menu items in the admin sidebar and implementing a custom contractor-focused dashboard for Sistem Jaya Abadi (SJA).

---

## 1. Problem Frame & Scope

### Problem
* The admin panel currently displays the complete default Tailwick dashboard template, containing pages of unused charts and boilerplate widgets (e.g. Paula Keenan, Ecommerce revenue, HR logs).
* The sidebar navigation is filled with template links (apps, CRM, ecommerce lists, customizer) which are confusing and irrelevant for SJA's internal CMS operations.
* The main dashboard `/dashboard` displays static template data instead of actual database metrics for SJA projects.

### Scope Boundary
* **In Scope:**
  * Redesign and replace `resources/views/dashboards/index.blade.php` with a clean SJA Contractor Dashboard showing real database stats, recent projects table, and quick shortcuts.
  * Clean up `resources/views/layouts/partials/sidenav.blade.php` to only keep: Dashboard, Projects, Profile, and Logout.
  * Update `app/Http/Controllers/RoutingController.php` to fetch projects statistics and pass them to the dashboard view.
  * Keep the Tailwick vertical layout styling and Preline UI styling intact.
* **Out of Scope:**
  * Deleting the other template files under `dashboards/*.blade.php` or `ecommerce/` since the user might want them as design references later. We will simply remove them from the sidebar navigation.

---

## 2. Key Technical Decisions

* **Metric Calculations:**
  * Count total, ongoing, and completed projects directly in `RoutingController@index`.
  * Calculate SEO optimization coverage percentage based on projects having both `meta_title` and `meta_description` populated.
* **UI Theme Consistency:**
  * Sidenav and Dashboard will continue to use the vertical template layout (`layouts.vertical`) to retain the premium look, dark mode compatibility, and icons (Lucide/Tabler Icons).

---

## 3. Implementation Units

### U1. Update RoutingController for Dashboard Data
* **Goal:** Load real database statistics and recent projects list to feed the custom dashboard.
* **Files:**
  * `app/Http/Controllers/RoutingController.php`
* **Approach:**
  * Import `App\Models\Project`.
  * Query total project count, completed project count, ongoing project count.
  * Query 5 most recent projects.
  * Calculate SEO coverage percentage.
  * Pass these variables to `dashboards.index`.
* **Test Scenarios:**
  * **Happy Path:** Accessing `/dashboard` successfully returns 200 with stats variables in view.
  * **Zero State:** If no projects exist in the database, stats show `0` and SEO coverage shows `0%` without throwing divisions by zero.

### U2. Clean up Sidebar Navigation
* **Goal:** Remove redundant templates and leave only SJA specific navigation links.
* **Files:**
  * `resources/views/layouts/partials/sidenav.blade.php`
* **Approach:**
  * Clean up the sidebar links, removing the accordion structures for dashboards, apps, customizer, ecommerce, HR, and UI elements.
  * Keep Logo branding pointing to `/dashboard`.
  * Add a clean menu section:
    * **Overview** -> Dashboard (link: `/dashboard`, icon: `layout-dashboard` or `monitor-dot`)
    * **Management** -> Projects (link: `/projects`, icon: `folder-kanban`)
    * **User Account** -> Profile (link: `/profile`, icon: `user`)
    * **Session** -> Logout (link: POST logout action, icon: `log-out`)
* **Test Scenarios:**
  * **Happy Path:** Sidenav loads cleanly and only displays SJA menu items.
  * **Link verification:** Clicking "Projects" goes to `/projects`, "Dashboard" goes to `/dashboard`, "Profile" goes to `/profile`.

### U3. Create SJA Contractor Dashboard View
* **Goal:** Replace the 1100-line static ecommerce dashboard with a custom SJA dashboard.
* **Files:**
  * `resources/views/dashboards/index.blade.php`
* **Approach:**
  * Extend `layouts.vertical` with title `Dashboard`.
  * Create 3 KPI cards with glowing borders or consistent design:
    * **Total Projects** (Icon: `folder-open`)
    * **Ongoing Projects** (Icon: `clock`)
    * **Completed Projects** (Icon: `check-circle-2`)
  * Add an SEO Optimization score ring or progress bar showing the percentage of projects with search engine tags filled.
  * Add a **Quick Actions** card:
    * Button "Add New Project" (`projects.create`)
    * Button "Preview Landing Page" (public url `/` in new tab)
  * Add a **Recent Projects** table:
    * List of latest 5 projects with title, location, status tag, and action buttons (View, Edit).
* **Test Scenarios:**
  * **Happy Path:** Dashboard page renders properly with dynamic variables.
  * **Table rendering:** If projects exist, they list in the table. If not, displays "No projects recorded yet".

---

## 4. Verification

* **Unit/Feature Tests:**
  * Add assertions in `tests/Feature/ProjectCrudTest.php` or create a new test `tests/Feature/DashboardTest.php` to verify that authenticated users can see the correct metrics and recent projects table on `/dashboard`.
* **Visual QA:**
  * Log in as admin, verify that the sidebar only contains the SJA links.
  * Verify the dashboard is clean, matches the PT. Sistem Jaya Abadi premium style, and displays the correct count of projects.
