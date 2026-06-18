# Requirements Document

## Introduction

This feature transforms the existing `clearance.php` resident view from a plain table into a full resident clearance dashboard. The dashboard will display summary stat cards computed from real request data, retain all existing document request functionality (new request modal, cancel request, table), and optionally surface quick-action tips — all styled consistently with the existing `resident.php` main dashboard (dark navy `#1d2448` primary, card-based layout, Poppins font, Font Awesome 5 icons, shared CSS classes from `style.css`).

No new backend routes or database schema changes are required. The controller already passes `$requests`, `$members`, `$user`, `$householdTotalIncome`, `$occupation`, and `$isEmployed` to the view.

## Glossary

- **Dashboard**: The `clearance.php` resident view rendered by `ClearanceController::residentIndex()`.
- **Stat_Card**: A visual card component displaying a single numeric metric with an icon and label, using the `.db-stat-card` CSS class.
- **Request**: A row in the `clearance_requests` table belonging to the logged-in resident, passed to the view as `$requests`.
- **New_Request_Modal**: The multi-step modal form (`#newModal`) that allows a resident to submit a new document request.
- **Cancel_Request_Modal**: The confirmation modal (`#cancelModal`) that allows a resident to cancel a pending request.
- **Requests_Table**: The `<table class="db-table">` listing all of the resident's document requests.
- **Quick_Tips_Section**: An optional informational section providing guidance on document types and processing times.
- **Status**: The `status` field of a request; one of `pending`, `approved`, `rejected`, or `released`.
- **Household**: The census household record linked to the resident's account via `household_no`.

---

## Requirements

### Requirement 1: Summary Stat Cards

**User Story:** As a resident, I want to see a summary of my document request activity at a glance, so that I can quickly understand the state of all my requests without scrolling through the table.

#### Acceptance Criteria

1. THE Dashboard SHALL display a stat cards row above the Requests_Table containing exactly four Stat_Cards: Total Requests, Pending, Approved, and Released/Rejected.
2. WHEN the Dashboard is loaded, THE counts displayed in each Stat_Card SHALL equal the counts derived from the `$requests` array at the time of page load.
3. WHEN `$requests` is empty, THE Dashboard SHALL display `0` in all four Stat_Cards.
4. IF the `$requests` array contains one or more Request records, THEN THE Stat_Card for "Total Requests" SHALL display the total count of all Request records for the resident.
5. IF the `$requests` array contains one or more Request records with Status `pending`, THEN THE Stat_Card for "Pending" SHALL display that count; otherwise it SHALL display `0`.
6. IF the `$requests` array contains one or more Request records with Status `approved`, THEN THE Stat_Card for "Approved" SHALL display that count; otherwise it SHALL display `0`.
7. IF the `$requests` array contains one or more Request records with Status `released` or `rejected`, THEN THE Stat_Card for "Released / Rejected" SHALL display the combined count; otherwise it SHALL display `0`.
8. THE Dashboard SHALL render each Stat_Card using the `.db-stat-card`, `.db-stat-icon`, `.db-stat-num`, and `.db-stat-label` CSS classes from `style.css` to match the visual style of `resident.php`.
9. IF a Request record has a Status value that is not `pending`, `approved`, `released`, or `rejected`, THEN it SHALL be counted in "Total Requests" only and SHALL NOT increment any named-status Stat_Card.

---

### Requirement 2: Retain New Request Functionality

**User Story:** As a resident, I want to submit a new document request from the clearance dashboard, so that I can request barangay documents without navigating away.

#### Acceptance Criteria

1. THE Dashboard SHALL display a "New Request" button that opens the New_Request_Modal when clicked.
2. WHILE the resident's `$members` array is empty, THE Dashboard SHALL hide the "New Request" button and display a warning message explaining that a linked household is required. The button visibility check SHALL use only the emptiness of the `$members` array, regardless of other household linkage states.
3. THE New_Request_Modal SHALL present all four sections simultaneously in a single scrollable form: member selection, document type selection, purpose selection (from a fixed list), and an optional notes field — in that order.
4. WHEN the resident submits the New_Request_Modal form, THE Dashboard SHALL POST to `/resident/clearance/store` with the selected `for_member`, `member_relationship`, `document_type`, `purpose`, and optionally `notes` fields.
5. IF `$isEmployed` is `true` (derived from the controller: occupation is non-empty and not one of "none", "n/a", "unemployed", "student", or "out-of-school"), THEN THE Dashboard SHALL visually disable the Certificate of Indigency option in the New_Request_Modal and display an ineligibility notice. No server-side validation for this rule is required in the view.
6. THE Dashboard SHALL include a CSRF token field in the New_Request_Modal form.
7. IF the form submission fails validation (e.g., missing required fields), THEN THE controller SHALL redirect back with an `error` flash message and the Dashboard SHALL display it using the `.db-alert--error` CSS class.
8. IF the resident requests a Certificate of Indigency and the household's total monthly income exceeds ₱12,000, THEN THE controller SHALL auto-reject the request and redirect to `/resident/clearance` with an `error` flash message stating the income threshold was exceeded.

---

### Requirement 3: Retain Cancel Request Functionality

**User Story:** As a resident, I want to cancel a pending document request, so that I can withdraw requests I no longer need.

#### Acceptance Criteria

1. WHEN a Request has Status `pending`, THE Requests_Table SHALL display a cancel action button for that row.
2. WHEN the resident clicks the cancel action button, THE Dashboard SHALL open the Cancel_Request_Modal displaying the document type of the selected Request.
3. WHEN the resident confirms cancellation in the Cancel_Request_Modal, THE Dashboard SHALL POST to `/resident/clearance/cancel/{id}` with a CSRF token; upon success the controller SHALL redirect to `/resident/clearance` with a `success` flash message.
4. IF the cancellation is rejected by the server (request not found, not owned by the resident, or not in `pending` status), THEN THE controller SHALL redirect to `/resident/clearance` with an `error` flash message and the request SHALL remain unchanged.
5. WHEN a Request has Status other than `pending`, THE Requests_Table SHALL NOT display a cancel action button for that row.
6. THE Requests_Table SHALL always display a view action button for rows with Status other than `pending`. The view button SHALL be rendered as disabled and SHALL perform no action on click until a view route is implemented.

---

### Requirement 4: Requests Table

**User Story:** As a resident, I want to see all my document requests in a table, so that I can review the details and status of each request.

#### Acceptance Criteria

1. THE Requests_Table SHALL display the following columns in order: # (zero-padded to 3 digits, e.g., `001`), For (member name on the first line and relationship on the second line in a smaller muted style), Document Type, Purpose, Date Filed, Est. Release, Status, and Action.
2. THE Dashboard SHALL render the Requests_Table using the `.db-table-wrap` and `.db-table` CSS classes from `style.css`.
3. WHEN `$requests` is empty, THE Requests_Table SHALL display an empty-state message with an icon prompting the resident to submit a new request.
4. THE Dashboard SHALL display a search input above the Requests_Table. WHEN the resident types in the search input, THE Dashboard SHALL filter visible rows client-side, case-insensitively, by matching the typed text against the For, Document Type, Purpose, and Status column values.
5. IF the search input contains text that matches no rows, THEN THE Requests_Table SHALL display a "no results" empty-state row instead of the filtered rows.
6. THE Dashboard SHALL render each request's Status as a styled badge using the appropriate `clr-badge--{status}` CSS class (`pending`, `approved`, `rejected`, `released`).
7. THE Dashboard SHALL format the Date Filed and Est. Release date columns as `M d, Y` (e.g., `Jun 05, 2025`), displaying `—` when Est. Release is null.

---

### Requirement 5: Quick Tips Section

**User Story:** As a resident, I want to see brief guidance about available document types and processing times, so that I know what to request and what to expect.

#### Acceptance Criteria

1. THE Dashboard SHALL display a Quick_Tips_Section between the Stat_Cards row and the Requests_Table.
2. THE Quick_Tips_Section SHALL list the three available document types — Barangay Clearance, Certificate of Residency, and Certificate of Indigency — each with a description of no more than 20 words and an estimated processing time of 2 business days.
3. THE Quick_Tips_Section SHALL be rendered with a white background, border-radius of at least 8px, a visible border using a color from the existing design palette, and the Poppins font — matching the `.svc-card` visual pattern used in `resident.php`.
4. IF the resident's `$members` array is empty, THEN THE Quick_Tips_Section SHALL be rendered in the page and not hidden.

---

### Requirement 6: Visual Consistency with Existing Dashboard

**User Story:** As a resident, I want the clearance dashboard to look and feel like the main resident dashboard, so that the experience is cohesive and familiar.

#### Acceptance Criteria

1. THE Dashboard SHALL use the `.db-body`, `.db-main`, `.db-content`, `.db-welcome`, `.db-stats`, `.db-section-title`, `.db-toolbar`, `.db-table-wrap`, and `.db-table` CSS classes from `style.css` for the corresponding structural layout elements.
2. THE Dashboard SHALL include the shared sidebar partial (`Views/dashboard/sidebar.php`) with `$active = 'clearance'` and `$role = 'resident'`.
3. THE Dashboard SHALL include the shared topbar partial (`Views/dashboard/topbar.php`).
4. IF the `success` session flashdata key is set, THEN THE Dashboard SHALL display the flash message using the `.db-alert--success` CSS class. IF the `error` session flashdata key is set, THEN THE Dashboard SHALL display the flash message using the `.db-alert--error` CSS class.
5. THE Dashboard SHALL use the Poppins font, Font Awesome 5 icons, and the dark navy (`#1d2448`) primary color for all UI elements not provided by the shared sidebar and topbar partials.
6. IF the viewport width is less than 600px, THEN THE Stat_Cards row SHALL collapse to a 2-column grid layout.
