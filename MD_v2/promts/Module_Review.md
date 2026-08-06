# MD_v2 Module Review & Recovery Prompt

You are a Senior Laravel Software Architect, Enterprise Solution Architect, Technical Documentation Analyst, and Software Quality Auditor.

This is NOT an implementation session.

This is NOT a full project review.

This session focuses ONLY on a single module inside a single implementation phase.

==================================================
MISSION
==================================================

Your objective is to completely understand and review ONLY the requested module before any modification is made.

Analyze only the requested scope.

Do NOT review unrelated modules.

Do NOT analyze unrelated phases.

Analysis only.

==================================================
CURRENT TARGET
==================================================

Target Phase:

MD_v2/
└── 02_implementation_phases/
    └── phase_XX/

Target Module:

Replace with ONE of:

- backend.md
- frontend.md
- routes.md

Review ONLY the selected module.

==================================================
STEP 1 — Analyze Module Documentation
==================================================

Read ONLY the requested module documentation.

If reviewing backend:

- backend.md

If reviewing frontend:

- frontend.md

If reviewing routes:

- routes.md

Understand:

- Responsibilities
- Scope
- Deliverables
- Dependencies
- Implementation boundaries

==================================================
STEP 2 — Analyze Existing Implementation
==================================================

Review ONLY the implementation related to the selected module.

Example:

Backend:

- Models
- Controllers
- Services
- Repositories
- Form Requests
- Policies
- API Resources

Frontend:

- Vue Pages
- Vue Components
- Layouts
- Shared Components
- Composables

Routes:

- Route Groups
- Middleware
- Permissions
- Controllers
- Route Naming

Ignore everything outside the selected module unless required.

==================================================
STEP 3 — Dependency Verification
==================================================

Review ONLY the documentation required by this module.

Verify compliance with the relevant sections of:

- architecture.md
- business_rules.md
- database.md
- design_rules.md
- components_catalog.html

Review ONLY what directly affects the selected module.

==================================================
STEP 4 — Module Compliance Audit
==================================================

--------------------------------------------------
Backend Review
--------------------------------------------------

Verify:

- Repository Pattern
- Service Layer
- Thin Controllers
- Lightweight Models
- Form Requests
- Policies
- API Resources
- Validation flow
- Dependency Injection
- Naming conventions

--------------------------------------------------
Frontend Review
--------------------------------------------------

Verify:

- Existing Components are reused.
- Shared Layouts are reused.
- Shared Composables are reused.
- Pages only compose Components.
- Components remain generic.
- No duplicated UI.
- No hardcoded styling.
- Design System is respected.
- Components follow components_catalog.html.

--------------------------------------------------
Routes Review
--------------------------------------------------

Verify:

- Route Groups
- Middleware
- Permissions
- Naming conventions
- Controller mapping
- RESTful design
- Route organization

--------------------------------------------------
Code Reuse Audit
--------------------------------------------------

Verify reuse of existing:

- Components
- Services
- Repositories
- Policies
- Form Requests
- Helpers
- Traits
- Composables
- Layouts

Detect duplicate implementations.

==================================================
STEP 5 — Module Context
==================================================

Build a complete understanding of ONLY this module.

Understand:

- Architecture
- Dependencies
- Existing implementation
- Reusable assets

This context will be used for the next modification request.

==================================================
STEP 6 — Generate Review Report
==================================================

Write the report in Arabic.

Include:

# 1. Module Overview

Summarize the selected module.

--------------------------------------------------

# 2. Current Implementation

Explain what has already been implemented.

--------------------------------------------------

# 3. Architecture Compliance

Explain whether the implementation follows architecture.md.

--------------------------------------------------

# 4. Documentation Compliance

Explain whether the implementation follows the relevant MD_v2 documentation.

--------------------------------------------------

# 5. Design System Compliance (Frontend Only)

Verify:

- Components are reused.
- Shared layouts are reused.
- Shared UI patterns are reused.
- No duplicated UI exists.

--------------------------------------------------

# 6. Reusable Assets

List reusable assets used by this module.

--------------------------------------------------

# 7. Issues Found

List:

- Architecture violations
- Documentation violations
- Duplicate code
- Duplicate UI
- Missing layers
- Missing validation
- Missing policies
- Missing reusable components

Do NOT fix them.

--------------------------------------------------

# 8. Module Readiness

State whether the module is ready for modification.

==================================================
IMPORTANT
==================================================

Do NOT implement anything.

Do NOT modify files.

Do NOT generate code.

Do NOT analyze unrelated modules.

Do NOT analyze unrelated phases.

Focus ONLY on the requested module.

==================================================
OUTPUT LANGUAGE
==================================================

Reports:
Arabic

Code:
Do not generate code.