# Requirements Document

## Introduction

This document covers two enhancements to the Household Census Registration Form in `census.php` (Step 2 — Family Information tab) of the Barangay BIS system built on CodeIgniter 4.

**Enhancement 1 — Registered Voter field for family members:** The "Are you a Registered Voter?" Yes/No radio field currently exists only for the Household Head (Step 1). This enhancement adds the same field to every family member row in Step 2: the Spouse section, the Child(ren) section (dynamic rows), and the Other Household Members section (dynamic rows). A database migration must add a `registered_voter TINYINT(1) DEFAULT 0` column to the `household_members` table, and the controller and model must be updated to persist the value.

**Enhancement 2 — Multiple family units under one household number:** The form currently has a "No. of Families in this Household" counter that generates family number codes (e.g., `12345-F1`, `12345-F2`) but provides no UI to enter data for additional families. This enhancement adds an "Add Another Family" button in Step 2 that appends a collapsible family block (Family Head, Spouse, Children, Other Members sub-sections) for each additional family unit. All additional family members are stored in `household_members` with a `family_index` column (INT, default 1) and a `family_head` relationship type for additional heads. The `num_families` counter in Step 1 stays in sync with the number of family blocks present in Step 2.

---

## Glossary

- **Census_Form**: The two-step modal form (`census.php`) used to register a household in the BIS system.
- **Household_Head**: The primary resident whose data is stored in the `households` table; always Family 1's head.
- **Family_Member**: Any person stored in the `household_members` table linked to a `household_no`.
- **Family_Block**: A collapsible UI section in Step 2 representing one nuclear family unit (head, spouse, children, other members).
- **Family_Index**: An integer column (`family_index`) on `household_members` that groups members by family unit within the same household (1 = main family, 2+ = additional families).
- **Additional_Family_Head**: A family member stored in `household_members` with `relationship = 'family_head'` and `family_index >= 2`.
- **Registered_Voter**: A boolean flag (`registered_voter TINYINT(1)`) indicating whether a person is a registered voter.
- **CensusController**: The CodeIgniter 4 controller at `app/Controllers/CensusController.php` that handles form submission and member CRUD.
- **HouseholdMemberModel**: The CodeIgniter 4 model at `app/Models/HouseholdMemberModel.php` that manages the `household_members` table.
- **Migration**: A CodeIgniter 4 database migration file under `app/Database/Migrations/`.

---

## Requirements

### Requirement 1: Add `registered_voter` Column to `household_members`

**User Story:** As a barangay secretary, I want the registered voter status of every family member to be stored in the database, so that the census data accurately reflects voter registration across all household members.

#### Acceptance Criteria

1. THE Migration SHALL add a `registered_voter TINYINT(1) DEFAULT 0` column to the `household_members` table, placed after the `philhealth_no` column, only if the column does not already exist.
2. THE Migration `down()` method SHALL drop the `registered_voter` column from `household_members` only if the column exists.
3. THE HouseholdMemberModel SHALL include `registered_voter` in its `$allowedFields` array so that the field is accepted during insert and update operations.
4. IF a family member is saved without an explicit `registered_voter` value, THEN the stored value SHALL be `0`.

---

### Requirement 2: Add `family_index` Column to `household_members`

**User Story:** As a barangay secretary, I want each family member record to carry a family unit index, so that multiple families sharing the same household number can be distinguished from one another.

#### Acceptance Criteria

1. THE Migration SHALL add a `family_index INT UNSIGNED DEFAULT 1` column to the `household_members` table, placed after the `registered_voter` column, only if the column does not already exist.
2. THE Migration `down()` method SHALL drop the `family_index` column from `household_members` only if the column exists.
3. THE HouseholdMemberModel SHALL include `family_index` in its `$allowedFields` array.
4. IF a family member is saved without an explicit `family_index` value, THEN the stored value SHALL be `1`.

---

### Requirement 3: Display Registered Voter Field for Spouse in Step 2

**User Story:** As a barangay secretary, I want to record whether the spouse is a registered voter directly in the Family Information tab, so that I do not need to edit the record separately after saving.

#### Acceptance Criteria

1. THE Census_Form SHALL display a "Registered Voter?" Yes/No radio field in the Spouse section of Step 2, using the same `pf-radio-row` styling as the Household Head's registered voter field in Step 1.
2. THE Census_Form SHALL render the spouse's registered voter radio with "No" pre-selected when the form is first opened.
3. WHEN the census form is submitted, THE CensusController SHALL read the `spouse_registered_voter` POST value (`'1'` = Yes, `'0'` or absent = No) and include `registered_voter` in the spouse member data array passed to `HouseholdMemberModel::replaceMembers()`.
4. IF the spouse section is left blank (the `spouse_last_name` POST value is empty or whitespace-only), THEN THE CensusController SHALL not save a spouse record, regardless of the registered voter radio value.

---

### Requirement 4: Display Registered Voter Field for Each Child Row in Step 2

**User Story:** As a barangay secretary, I want to record whether each child is a registered voter in the Family Information tab, so that voter registration data is complete for all household members.

#### Acceptance Criteria

1. THE Census_Form SHALL display a "Registered Voter?" Yes/No radio group in each child row of the Child(ren) section, appended as the last data column before the delete button.
2. THE Census_Form SHALL render each child row's registered voter radio with "No" pre-selected, both for the initial static row and for rows added via the "Add Row" button.
3. WHEN the census form is submitted, THE CensusController SHALL read `child_registered_voter[]` POST values and include `registered_voter` for each child member data entry, defaulting to `0` if the value is absent for a given index.
4. THE Census_Form SHALL include the registered voter radio in the `childRowHTML()` JavaScript template function so that dynamically added rows also contain the field with "No" pre-selected.

---

### Requirement 5: Display Registered Voter Field for Each Other Household Member Row in Step 2

**User Story:** As a barangay secretary, I want to record whether each other household member is a registered voter, so that the census captures complete voter data for the entire household.

#### Acceptance Criteria

1. THE Census_Form SHALL display a "Registered Voter?" Yes/No radio group in each row of the Other Household Members section, appended as the last data column before the delete button.
2. THE Census_Form SHALL render each other member row's registered voter radio with "No" pre-selected, both for the initial static row and for rows added via the "Add Row" button.
3. WHEN the census form is submitted, THE CensusController SHALL read `other_registered_voter[]` POST values and include `registered_voter` for each other member data entry, defaulting to `0` if the value is absent for a given index.
4. THE Census_Form SHALL include the registered voter radio in the `otherRowHTML()` JavaScript template function so that dynamically added rows also contain the field with "No" pre-selected.

---

### Requirement 6: Persist Registered Voter for Members via `addMember()` and `updateMember()`

**User Story:** As a barangay secretary, I want the registered voter status to be saved and updated when I add or edit individual members from the household detail page, so that the data stays accurate over time.

#### Acceptance Criteria

1. WHEN `CensusController::addMember()` is called, THE CensusController SHALL read the `registered_voter` POST value and include it in the insert data array passed to `HouseholdMemberModel::insert()`.
2. WHEN `CensusController::updateMember()` is called, THE CensusController SHALL read the `registered_voter` POST value and include it in the update data array passed to `HouseholdMemberModel::update()`.
3. IF the `registered_voter` POST value is absent in either method, THEN THE CensusController SHALL use `0` as the value.
4. THE household detail view (`household.php`) Add Member modal and Edit Member modal SHALL each include a "Registered Voter?" Yes/No radio field so that the POST value is available to the controller.

---

### Requirement 7: Add Another Family Button and Dynamic Family Blocks in Step 2

**User Story:** As a barangay secretary, I want to add multiple family units under the same household number in a single form submission, so that I can register extended families sharing one address without creating separate household records.

#### Acceptance Criteria

1. THE Census_Form SHALL display an "Add Another Family" button at the bottom of Step 2, below the Other Household Members section and above the certification box.
2. WHEN the "Add Another Family" button is clicked, THE Census_Form SHALL append a new Family_Block labeled "Family N" (where N is the next sequential integer starting from 2) below the existing Family_Blocks.
3. THE Family_Block SHALL contain four sub-sections in order: Family Head, Spouse, Child(ren) (with "Add Row" button), and Other Household Members (with "Add Row" button) — each with the same fields as the corresponding Family 1 sections, including the Registered Voter radio.
4. WHEN the remove button on an additional Family_Block is clicked, THE Census_Form SHALL remove that block from the DOM and re-number all remaining additional blocks sequentially (e.g., if Family 2 is removed, Family 3 becomes Family 2).
5. THE Census_Form SHALL NOT render a remove button on the Family 1 block.
6. WHEN a Family_Block is added or removed, THE Census_Form SHALL update the value of the `num_families` input field in Step 1 to equal the current total count of Family_Blocks present in Step 2.

---

### Requirement 8: Save Additional Family Members to `household_members`

**User Story:** As a barangay secretary, I want the data entered for additional family units to be saved to the database, so that all families in a shared household are recorded.

#### Acceptance Criteria

1. WHEN the census form is submitted with one or more additional Family_Blocks, THE CensusController SHALL save each Additional_Family_Head to `household_members` with `relationship = 'family_head'` and `family_index` equal to the block's family number (2, 3, …).
2. WHEN the census form is submitted, THE CensusController SHALL save each additional family's spouse, children, and other members to `household_members` with `family_index` equal to their Family_Block's family number.
3. THE CensusController SHALL assign `family_index = 1` to all members of the main family (the Household Head's spouse, children, and other members saved in the existing Step 2 sections).
4. IF an additional Family_Block's head `last_name` POST value is empty or whitespace-only, THEN THE CensusController SHALL skip saving that entire Family_Block — including its head, spouse, children, and other members.

---

### Requirement 9: Sync `num_families` Counter with Family Blocks

**User Story:** As a barangay secretary, I want the "No. of Families in this Household" counter to automatically reflect the number of family blocks I have added, so that the stored count is always accurate without manual entry.

#### Acceptance Criteria

1. WHEN the census form is opened, THE Census_Form SHALL set the `num_families` input value to `1` and render exactly one Family_Block (Family 1).
2. WHEN a Family_Block is added via the "Add Another Family" button, THE Census_Form SHALL set the `num_families` input value to the new total count of Family_Blocks.
3. WHEN a Family_Block is removed, THE Census_Form SHALL set the `num_families` input value to the new total count of Family_Blocks, with a minimum value of `1`.
4. THE `num_families` input field in Step 1 SHALL be read-only (not directly editable by the user) so that its value is always driven by the Family_Block count in Step 2.
5. WHEN the census form is submitted, THE CensusController SHALL use the `num_families` POST value to set `num_families` on the `households` record, applying a server-side floor of `1`.

---

### Requirement 10: Collapsible Family Blocks UI

**User Story:** As a barangay secretary, I want additional family blocks to be collapsible, so that the form remains manageable when multiple families are present.

#### Acceptance Criteria

1. WHEN a new Family_Block (Family 2 or above) is appended to Step 2, THE Census_Form SHALL render its content area (the four sub-sections) with `display:none` so the block appears collapsed.
2. WHEN the Family_Block header is clicked, IF the content area is currently hidden (`display:none`), THEN THE Census_Form SHALL set it to visible (`display:block`); IF the content area is currently visible, THEN THE Census_Form SHALL set it to hidden (`display:none`).
3. THE Census_Form SHALL display the family block label ("Family 2", "Family 3", etc.) in the block header at all times, regardless of the collapsed or expanded state.
4. THE Census_Form SHALL display the Family 1 block (main family) with its content area always visible and SHALL NOT render a collapse toggle on the Family 1 header, preserving the existing layout.
5. WHEN a Family_Block is re-numbered after a sibling block is removed, THE Census_Form SHALL update the label in the block header to reflect the new number.
