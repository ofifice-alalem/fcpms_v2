# 🎨 Unified Design Rules – Spatial UI v3.0

> **Primary Visual Reference:**  
> All styles, dimensions, and interactions in this system are derived from the main design file:  
> [`FCPMS Design System v3.0.html`](./FCPMS%20Design%20System%20v3.0.html)  
> All components and interfaces must exactly match this file in appearance and behavior, while adhering to the rules below.

---

## 📐 Layout Rules (LY)

| # | Rule |
|---|------|
| LY-001 | All pages use a unified Layout (Sidebar + Header + Content) |
| LY-002 | Every page contains: Page Title + Brief Description + Breadcrumb when needed |
| LY-003 | No more than one scroll area (Nested Scroll) per page unless functionally required |
| LY-004 | All pages are responsive and support different screen sizes |
| LY-005 | Uniform spacing between elements across all pages (multiples of 4px) |

---

## 🧭 Navigation Rules (NV)

| # | Rule |
|---|------|
| NV-001 | Access to all main screens must be through a clear navigation system |
| NV-002 | No more than one way to access the same screen unless functionally justified |
| NV-003 | Breadcrumb must reflect the user's actual path |
| NV-004 | Preserve page state (Filters/Search/Pagination) when returning from detail screens |

---

## 📝 Forms Rules (FM)

| # | Rule |
|---|------|
| FM-001 | Use Modal for simple forms (up to ~5 fields) |
| FM-002 | Use Drawer for medium-sized forms while preserving page context |
| FM-003 | Use Full Page for complex forms (Tabs, Builder, Wizard) |
| FM-004 | All required fields marked with `*` (red color) |
| FM-005 | Group related fields into clear Sections |
| FM-006 | Prevent saving if data is invalid |
| FM-007 | Display validation messages directly next to the associated field |

---

## 📊 Tables Rules (TB)

| # | Rule |
|---|------|
| TB-001 | All tables support search when needed |
| TB-002 | Use Filters when data filtering is required |
| TB-003 | Use Pagination or Lazy Loading for large datasets |
| TB-004 | Every table includes an Actions column |
| TB-005 | Order columns by importance (not by database order) |

---

## 🪟 Dialog & Drawer Rules (DL)

| # | Rule |
|---|------|
| DL-001 | Any delete action must go through a confirmation dialog |
| DL-002 | No more than one Modal can be open at a time |
| DL-003 | All Dialogs contain: Title, Description (if needed), Execute button, Cancel button |
| DL-004 | Warn the user if there are unsaved changes before closing |

---

## 🎯 Actions Rules (AC)

| # | Rule |
|---|------|
| AC-001 | Clearly distinguish primary actions (Primary Buttons) |
| AC-002 | Use different colors for dangerous actions (Delete, Deactivate) |
| AC-003 | Prevent executing the same action more than once during submission |
| AC-004 | Show a loading indicator for long-running operations |

---

## 🔔 Feedback Rules (FB)

| # | Rule |
|---|------|
| FB-001 | Show success message (Toast) after completing important operations |
| FB-002 | Show clear error message when an operation fails |
| FB-003 | Display Empty State for pages with no data |
| FB-004 | Display Loading State (Skeleton) while data is being fetched |

---

## 🔐 Permission Rules (PM)

| # | Rule |
|---|------|
| PM-001 | Hide actions that the user is not authorized to perform |
| PM-002 | Prefer hiding unauthorized buttons rather than disabling them |

---

## 🔄 Consistency Rules (CS)

| # | Rule |
|---|------|
| CS-001 | Use the same operation names across all parts of the system |
| CS-002 | Pages displaying similar data must use the same presentation style |
| CS-003 | Any Component designed must be reusable |

---

## 🎨 Dimensions in v3.0 (According to Design File)

| Element | Value | Notes |
|---------|-------|-------|
| Dropdown Height | `56px` (h-14) | Improves interaction and tapping |
| Card Corners | `30px` (rounded-3xl) | Smooth glass effect |
| Primary Button Corners | `20px` (rounded-2xl) | With hover effect |
| Input Corners | `18px` | Consistent with design system |
| Spacing | Multiples of `4px` | Like Tailwind |
| Glass Effect | `backdrop-filter: blur(40px)` | With transparent backgrounds |
| Shadows | `box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15)` | Moderate depth |

---

## 🌗 Dark Mode Support

- Use `.dark` class on `<html>` element
- Colors change automatically via CSS Variables
- Dark backgrounds: `radial-gradient(circle at 50% 0%, #32323f, #13131a)`
- Text: `#c9d1d9` in dark mode
- Cards: 35% transparency with subtle borders
- All colors and contrasts are defined in the main design file

---

## 🧩 Core Components

| Component | Usage | Related Rule |
|-----------|-------|--------------|
| `SpatialCard` | Glass container | LY-001, LY-005 |
| `SpatialTable` | Tabular data display | TB-001 to TB-005 |
| `SpatialButton` | Interactive buttons | AC-001 to AC-004 |
| `SpatialInput` | Input fields | FM-004, FM-007 |
| `SpatialDropdown` | Dropdown menus | FM-002, FM-004 |
| `SpatialModal` | Popup dialogs | DL-001 to DL-004 |
| `SpatialDrawer` | Side sliding panel | FM-002, DL-004 |
| `SpatialToast` | Notifications | FB-001, FB-002 |
| `SpatialProgressBar` | Progress indicator | — |
| `SpatialStatusPill` | Status badge | — |
| `SpatialEmptyState` | No data state | FB-003 |
| `SpatialSkeleton` | Loading skeleton | FB-004 |
| `TaskOptionCard` | Task selection | — |
| `TaskChecklist` | Checklist | — |
| `TaskImageUpload` | Image upload | — |
| `TaskConditionalField` | Conditional field | BR-035 |

---

## 📌 External References

- **Primary Design File:** [`FCPMS Design System v3.0.html`](./FCPMS%20Design%20System%20v3.0.html) – refer for precise visual details.
- **Business Rules:** [`business_rules.md`](../00_architecture_and_rules/business_rules.md) – especially BR-035 (Conditional Fields)
- **System Architecture:** [`architecture.md`](../00_architecture_and_rules/architecture.md) – Frontend layers
- **Database:** [`database.md`](../00_architecture_and_rules/database.md) – Task and component tables

---

> ✅ These rules are the primary reference for developing all user interfaces in the system.
> Any new component or page must adhere to these rules and exactly match the design of `FCPMS Design System v3.0.html`.
