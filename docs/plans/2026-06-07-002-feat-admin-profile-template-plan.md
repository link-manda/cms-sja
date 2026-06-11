---
title: feat/admin-profile-template
created: 2026-06-07
status: active
---

# Plan: Admin Profile Integration with Tabbed Tailwick Template

This plan defines the process for replacing the default Laravel Breeze profile edit layout with a cohesive, tabbed admin profile page that matches SJA's vertical theme styling.

---

## 1. Problem Frame & Scope

### Problem
* The current profile edit page (`resources/views/profile/edit.blade.php`) uses the default Laravel Breeze `<x-app-layout>` layout, which breaks the consistent sidebar, topbar, and dark/light theme of the Tailwick template.
* The forms inside `profile/partials/` contain generic Tailwind inputs and classes that mismatch the sleek and premium styled forms used elsewhere in SJA admin panel (such as the Projects forms).

### Scope Boundary
* **In Scope:**
  * Refactor `resources/views/profile/edit.blade.php` to extend `layouts.vertical` instead of `<x-app-layout>`.
  * Redesign the profile edit layout into a modern Tabbed Interface:
    * Tab 1: **Profile Information** (update name and email)
    * Tab 2: **Change Password** (update password)
    * Tab 3: **Danger Zone** (delete account)
  * Restyle all forms and input fields in the profile page to match Tailwick UI styles (e.g. `form-input`, standard buttons, headers, error classes).
  * Ensure full functional parity with Laravel's built-in Profile actions (success alerts, email verification status, validation errors).
* **Out of Scope:**
  * Modifying the backend logic in `ProfileController.php` since the existing methods (`edit`, `update`, `destroy`) work correctly.

---

## 2. Key Technical Decisions

* **Tabs Implementation:**
  * Use a simple vanilla JavaScript tab switcher in the blade view. Clicking tabs will show/hide the corresponding sections (`#profile-info`, `#change-password`, `#danger-zone`) and update active CSS classes (e.g. text/border highlights) for visual feedback.
* **UI Theme Consistency:**
  * Ensure all text, buttons, and alert messages use English to match the admin language guideline.
  * Form styling matches standard form patterns used in `projects/edit.blade.php`.

---

## 3. Implementation Units

### U1. Refactor Profile Edit View (`edit.blade.php`)
* **Goal:** Extend the core admin layout and create the tabs shell container.
* **Files:**
  * `resources/views/profile/edit.blade.php`
* **Approach:**
  * Replace `<x-app-layout>` with `@extends('layouts.vertical', ['title' => 'Profile'])`.
  * Wrap content with `@section('content')` and include the page title partial.
  * Design a card layout containing:
    * A header menu with 3 tabs: "Profile Information", "Update Password", and "Danger Zone".
    * A card body containing the 3 sections, visible depending on the selected tab.
  * Add vanilla JavaScript at the bottom of the view to manage the tab toggle logic (hiding inactive sections, adding border-bottom active colors to the active tab button).
* **Test Scenarios:**
  * **Happy Path:** Navigating to `/profile` loads the SJA theme with tab selection.
  * **Tab Switch:** Clicking "Update Password" shows the password form and hides profile information.

### U2. Restyle Profile Info Form Partials
* **Goal:** Update the forms to use Tailwick-compliant input classes and buttons.
* **Files:**
  * `resources/views/profile/partials/update-profile-information-form.blade.php`
  * `resources/views/profile/partials/update-password-form.blade.php`
  * `resources/views/profile/partials/delete-user-form.blade.php`
* **Approach:**
  * **Update Profile Info:**
    * Wrap fields with `space-y-4`.
    * Inputs styled with class `form-input`.
    * Save button styled as `btn bg-primary text-white`.
    * Verification link styled as `text-sm text-primary hover:underline`.
  * **Update Password:**
    * Match layout with input classes and labels.
    * Use `btn bg-primary text-white` for submission.
  * **Delete User:**
    * Make it look like a real "Danger Zone" section: warnings in red, description of what happens, delete button styled as `btn bg-danger text-white`.
* **Test Scenarios:**
  * **Happy Path:** Updating name/email saves successfully and displays the checkmark alert.
  * **Validation Fail:** Submitting mismatching password displays error messages styled with `x-input-error`.

---

## 4. Verification

* **Unit/Feature Tests:**
  * Run the existing profile tests `tests/Feature/ProfileTest.php` to ensure the authentication and form submission remain 100% correct.
* **Visual QA:**
  * Open `/profile` in browser, verify tabs work instantly, verify form fields look modern, select each tab, and test updating the profile name.
