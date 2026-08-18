# ProcurePilot — How Every Module Works

A complete walkthrough of the ProcurePilot procurement platform: what each module does, the screens inside it, the lifecycle of records, and who can use it.

---

## Contents

1. [Logging In](#1-logging-in)
2. [Roles, Access & Permissions](#2-roles-access--permissions)
3. [Dashboard](#3-dashboard)
4. [Procurement Requests](#4-procurement-requests)
5. [Procurement Plans](#5-procurement-plans)
6. [Budgets](#6-budgets)
7. [Suppliers](#7-suppliers)
8. [Categories](#8-categories)
9. [Tenders](#9-tenders)
10. [Bids & Evaluation](#10-bids--evaluation)
11. [Awards](#11-awards)
12. [Contracts](#12-contracts)
13. [Purchase Orders](#13-purchase-orders)
14. [Supplier Invoices](#14-supplier-invoices)
15. [Reports](#15-reports)
16. [Compliance](#16-compliance)
17. [Approval Workflow](#17-approval-workflow)
18. [Audit Trail](#18-audit-trail)
19. [Users](#19-users)
20. [Role & Access](#20-role--access)
21. [Settings](#21-settings)
22. [Billing & Subscription](#22-billing--subscription)
23. [Notifications & Profile](#23-notifications--profile)
24. [End-to-End Workflows](#24-end-to-end-workflows)

---

## 1. Logging In

1. Open the system URL in a browser.
2. Enter your **email** and **password**, click **Log in**.
3. Use **Forgot your password?** to reset via email if needed.

The menu you see is based on your role and the permissions granted to it. If a module isn't shown, your role has no access to it.

---

## 2. Roles, Access & Permissions

### The roles

| Role | What they do |
|------|--------------|
| **Super Admin** | Full access to everything, plus the power to grant the Super Admin role and edit the Role & Access matrix. |
| **Admin** | Full operational access to all modules, users and settings. |
| **Procurement Officer** | Runs day-to-day procurement: requests, plans, tenders, evaluation, contracts, POs, invoices, budgets, suppliers, categories. |
| **Approver** (Tenders Board) | Approves/rejects plans, requests, tenders, awards, POs, invoices and supplier registrations. |
| **Auditor** | Read-only oversight: all procurement records plus the Audit Trail. |
| **Staff** | Light user: views tenders and categories, submits procurement requests. |
| **Supplier** | External account: views open tenders, submits bids. |

### How permission is decided

- **Super Admin and Admin bypass all permission checks** — they can do everything.
- All other roles are granted access **per module** with an action matrix:

```
V = View    C = Create    E = Edit    D = Delete    A = Approve
```

For example, the Procurement Officer default grants are:
- `plans` `tenders` `contracts` `purchase-orders` `requests` `invoices` `budgets` `suppliers` `categories` → **v c e**
- `awards` `reports` `workflow` `compliance` → **v**
- no access to `users`, `audit`, `billing`, `settings`

The Approver gets **v a** on plans, tenders, awards, contracts, requests, POs, invoices and suppliers (view + approve, no create/edit).

The Auditor gets **v** on nearly everything plus `audit`.

A Super Admin can change any of this under **Role & Access** (see section 20).

---

## 3. Dashboard

The **Dashboard** is the landing page and is different for each role:

- **Admin / Super Admin** — organisation-wide KPIs: active tenders, pending approvals, committed spend, supplier count, open invoices, budget utilisation, and the **Monthly Procurement Spend (2026)** chart.
- **Procurement Officer** — their in-flight work: draft tenders, submitted requests, open invoices, budget status.
- **Approver** — a **pending approvals** panel listing everything waiting for their decision (requests, plans, tenders, awards, POs, invoices, suppliers) with quick links.
- **Auditor** — read-only snapshot: spend, tenders, contracts, compliance stats.
- **Staff** — their own requests and open tenders.
- **Supplier** — open tenders and the status of their bids.

The **Monthly Procurement Spend (2026)** card is a real Chart.js interactive bar chart. Hover over a bar to see the spend value for that month. Clicking chart cards navigates to the relevant module.

---

## 4. Procurement Requests

A procurement request records a need before it becomes a formal procurement.

### How it works

1. **Create** (Staff or Procurement Officer) → **New Request**:
   - Title, justification, requesting department, category, budget code, required date, priority (normal/high/critical), estimated cost and currency.
   - Add **line items**: description, quantity, unit, estimated unit cost (total auto-calculates).
2. While editing, the request stays in **Draft**.
3. **Submit** the request → status becomes **Pending Approval** and it enters the approval workflow.
4. Approvers (per the Approval Workflow levels) **Approve** or **Reject** with a comment.
5. Once **Approved**, it can be referenced when building plans and tenders.

### Status lifecycle

`Draft` → `Pending Approval` → `Approved` / `Rejected`

### Screens
- **List** — search, filter by status/department/priority.
- **Create / Edit** — form + line items.
- **Detail** — full request, items, approval trail, submit/approve/reject actions (permission-dependent).

---

## 5. Procurement Plans

Plans consolidate the year's intended procurement into one approved document (required by §27 PPA 2007).

### How it works

1. **Create a plan** → title, fiscal year (e.g. `2025/2026`), description.
2. **Add plan items**: title, category, estimated cost, quantity, procurement method (open competitive / restricted / direct), priority and expected date.
3. **Submit** the plan → **Awaiting Approval**.
4. An approver **Approves** or **Rejects** it.
5. Approved plans drive tendering — new tenders should map back to approved plan items.

### Status lifecycle

`Draft` → `Awaiting Approval` → `Approved` / `Rejected`

### Screens
- **List** — all plans, filter by fiscal year/status.
- **Create / Edit** — plan details.
- **Detail** — items table, submit/approve/reject, add/remove items.

---

## 6. Budgets

Budgets track allocated, committed and spent amounts per department and category.

### How it works

1. **Create a budget** → name, fiscal year, department, category (operating / capital), allocated amount, committed amount, spent amount, currency, status (Draft/Active/Closed).
2. **Commit** — lock funds against a planned or in-flight purchase (increases committed amount).
3. **Release** — free up committed funds (reduces committed amount).
4. Budgets surface on the dashboard and reports so you can compare spend against allocation.

### Status lifecycle

`Draft` → `Active` → `Closed`

### Screens
- **List** — allocated vs spent summary per budget.
- **Create / Edit**.
- **Detail** — commit / release actions.

---

## 7. Suppliers

The supplier registry holds vetted vendors with their registration documents and compliance status.

### How it works — registration

1. **Register Supplier** → company details: name, registration number (e.g. `RC 482195`), tax ID, email, phone, address, country, category, bank details (account name, bank, account number) and certifications.
2. **Required Documents** — every requirement configured by admin in **Settings → Supplier Registration Requirements** appears as its own upload field, labelled **Required** or **Optional**. Upload the file(s) for each one.
3. **Register** → supplier is created as **Pending Vetting**.

### How it works — compliance checklist

On every supplier profile there is a **Compliance Checklist**:
- Lists each required document with a green ✓ (uploaded) or red ✗ (missing).
- Shows a summary such as **"3/4 required met"** and a **Complete** badge when all required documents are present.
- Click a document chip to **Download** it, or **Remove** it.

### How it works — vetting

- An approver/admin opens the profile and clicks **Approve & Vet**, or uses the **Change Status** dropdown (Approved / Pending / Suspended / Blacklisted) with review notes.
- Only **Approved** suppliers can bid on tenders.

### Status lifecycle

`Pending Vetting` → `Approved` / `Suspended` / `Blacklisted`

### Screens
- **List** — search by name/email/reg number, filter by status.
- **Create / Edit** — form with per-requirement upload fields.
- **Detail** — company info, bank details, compliance checklist, supporting documents (download/remove), vetting status, contracts, bids.

---

## 8. Categories

Procurement categories tag everything (suppliers, requests, plans, tenders, budgets).

### How it works

- Each category has a **name**, **code** and **type** (goods / services / works).
- Examples: `Marine & Port Equipment (MPE)`, `Consulting Services (CS)`, `ICT & Software (ICT)`, `Port Infrastructure Works (PIW)`.
- Admins create/edit/delete categories; they appear as dropdowns throughout the system.

---

## 9. Tenders

Tenders are the formal bidding process for a defined requirement.

### How it works — creating

1. **New Tender** → reference, title, description, category, type (open / restricted), method, budget, currency, dates (published / closing / opening), and evaluation method (**Weighted Score** or **Lowest Price**).
2. **Add tender items** → description, quantity, unit, estimated unit price (line totals auto-calculate).
3. For weighted scoring, **add evaluation criteria** → name, **weight** (should total 100) and **max score**.

### How it works — lifecycle

| Action | Effect |
|--------|--------|
| Create | Tender is **Draft** |
| **Publish** | Becomes **Published** — visible to suppliers, bids accepted |
| Closing date passes | **Closed** (or close manually) |
| **Evaluate** | Becomes **Under Evaluation** |
| Award approved | Becomes **Awarded** |
| **Cancel** | Becomes **Cancelled** (bidding stopped) |

### Screens
- **List** — search, filter by status/category.
- **Create / Edit** — details, items, criteria.
- **Detail** — full tender, items, criteria, bids received, publish/close/cancel/evaluate actions.

---

## 10. Bids & Evaluation

### How it works — bidding (Supplier role)

1. Supplier opens a **Published** tender.
2. **Submit Bid** → itemised pricing per tender item (quantity × unit price), total amount, and a compliance declaration.
3. Bid is created as **Submitted** and linked to the tender.

### How it works — evaluation (Procurement Officer)

1. On the tender detail page, click **Evaluate**.
2. Score each bid against every criterion (score out of the criterion's max score), with optional comments.
3. Scores are stored per evaluator; the **weighted total** ranks bids.
4. Bid status moves **Submitted → Evaluated**.
5. Click **Recommend Award** on the winning bid to create a recommended award.

### How it works — withdraw (Supplier)

A supplier can **Withdraw** their own bid before award; it becomes **Withdrawn**.

### Bid status lifecycle

`Submitted` → `Evaluated` → `Awarded` / `Rejected` / `Withdrawn`

---

## 11. Awards

The formal decision to award a tender to a bidder.

### How it works

1. A recommended award is created from the winning bid (amount, currency, justification).
2. Status is **Recommended**.
3. An approver **Approves** or **Declines** the award with notes.
4. Once approved, **Create Contract** turns the award into a contract.

### Status lifecycle

`Recommended` → `Approved` / `Declined`

### Screens
- **List** — all awards with tender, supplier, amount, status.
- **Detail** — award details, approve/decline, create contract.

---

## 12. Contracts

Contracts formalise an approved award into a binding agreement.

### How it works

1. **Create a contract** (linked to an award, supplier and tender) → reference, title, description, value, currency, start/end dates, payment terms.
2. Upload **contract documents** (signed copy etc.) → download/remove anytime.
3. Add **milestones** → title, due date, amount. Mark each **Complete** as it is delivered.
4. Lifecycle:
   - **Activate** a contract → **Active**.
   - **Complete** it when obligations are met → **Completed**.
   - **Terminate** it early → **Terminated**.
   - Contracts that pass their end date show as **Expired**.

### Status lifecycle

`Draft` → `Active` → `Completed` / `Terminated` / `Expired`

### Screens
- **List** — active/completed/expired view.
- **Create / Edit**.
- **Detail** — milestones, documents, activate/complete/terminate actions.

---

## 13. Purchase Orders

POs authorise the supplier to deliver and invoice.

### How it works

1. **Create PO** (linked to a contract/tender and supplier) → reference, title, description, order date, expected delivery, total, currency.
2. **Add PO items** → description, quantity, unit, unit price, total.
3. Lifecycle:
   - **Approve** the PO → **Approved**.
   - **Issue** it to the supplier → **Issued**.
   - Record **goods receipt** → **Partially Received** (partial qty) or **Received** (full qty). Received quantities feed three-way matching.
   - **Cancel** a PO that shouldn't proceed → **Cancelled**.

### Status lifecycle

`Draft` → `Approved` → `Issued` → `Partially Received` → `Received` (or `Cancelled`)

### Screens
- **List** — filter by status/supplier.
- **Create / Edit**.
- **Detail** — items with received quantities, approve/issue/receive/cancel actions.

---

## 14. Supplier Invoices

Invoices claim payment from the supplier against delivered goods.

### How it works — create

1. **New Invoice** → number, supplier (optionally linked to a PO and contract), invoice date, due date, subtotal, tax, total, currency, notes.
2. Add **invoice items** referencing PO line items.

### How it works — the approval & matching path

| Step | Action | Status |
|------|--------|--------|
| 1 | Create | **Pending** |
| 2 | **Verify** (confirm genuine) | **Verified** |
| 3 | **Match** against the PO (three-way: PO ↔ goods receipt ↔ invoice) | **Matched** (or Unmatched) |
| 4 | **Approve** for payment | **Approved** |
| 5 | **Pay** | **Paid** |
| — | **Reject** with reason | **Rejected** |

### Status lifecycle

`Pending` → `Verified` → `Matched` → `Approved` → `Paid` (any stage may be `Rejected`)

### Screens
- **List** — filter by status/match status.
- **Create / Edit**.
- **Detail** — line items, match status, verify/match/approve/pay/reject actions.

---

## 15. Reports

Central reporting with filters and export.

### How it works

1. Choose a **report type**:

```
Overview · Procurement Requests · Tenders · Contracts · Purchase Orders ·
Invoices · Payments · Budgets · Suppliers · Goods Receipts
```

2. Apply filters — date range, category, supplier, department, status (options adapt to the report type).
3. View the KPI summary and the data table.
4. **Export** the report (available formats per report).

The Overview also shows the 8 most recent audit entries so management sees recent activity at a glance.

---

## 16. Compliance

A compliance dashboard aligned with the **Public Procurement Act 2007 (Nigeria)** and international best practice (World Bank, UN, ISO 20400).

### How it works

- **Stat cards** — registered suppliers, vetted & approved, open tenders, active contracts.
- **Compliance Framework** — the rules the system enforces: annual procurement planning (§27), open competitive bidding (§24), supplier vetting & certification, Tenders Board approval (§34–40), full audit trail, sustainable procurement.
- **Procurement Statistics** — total tenders, bids, contracts, POs and the **supplier approval rate**.
- **Transparency Indicators** — open bidding ratios and competition levels.

Read-mostly module for audit, management and approval roles.

---

## 17. Approval Workflow

Defines *who approves what* based on value.

### How it works

1. Admin defines **approval levels**, each with:
   - **Name** (e.g. Department Head, Procurement Director, Tenders Board).
   - **Sequence** (the order approvals happen).
   - **Role** that acts on the level (approver / admin / etc.).
   - **Min / Max amount** range the level applies to.
2. When a document is submitted, the system creates an **Approval Record** for every level whose range covers the document value.
3. Approvers act level-by-level in sequence until the document is fully approved.
4. The **Workflow** screen shows: departments, configured levels, **pending approvals**, and the **full approval history**.

### Example levels

```
Level 1  Department Head         ₦0      – ₦10M     (approver)
Level 2  Procurement Director    ₦10M    – ₦50M     (admin)
Level 3  Tenders Board           ₦50M+             (approver)
```

A ₦12.5M request therefore needs Level 1 **and** Level 2 approval.

---

## 18. Audit Trail

Every significant action is recorded immutably.

### How it works

- Logged actions include: supplier registration, request/plan/tender/award submission and decisions, contract/P.O/invoice actions, permission changes, gateway updates.
- Each record shows **who** (user), **what** (event), **when** (timestamp), plus the before/after state where relevant.
- View the full trail under **Audit Trail** (admin/auditor); the newest entries also appear on the Reports Overview.

---

## 19. Users

Manage user accounts and roles.

### How it works

1. **Add User** → name, email, role, title.
2. **Roles available** depend on your own role:
   - A regular Admin can assign Admin, Procurement Officer, Approver, Auditor, Staff, Supplier.
   - **Only a Super Admin can assign the Super Admin role** — it is hidden for everyone else.
3. **Edit** user details/role, or **Remove** a user.
4. Role changes take effect on the user's next request (permissions are checked live).

---

## 20. Role & Access

Super Admin only. A **V/C/E/D/A matrix** to fine-tune permissions.

### How it works

- Rows = modules (Dashboard, Plans, Tenders, Awards, Contracts, Purchase Orders, Requests, Invoices, Budgets, Suppliers, Categories, Reports, Workflow, Compliance, Audit, Users, Billing, Settings).
- Columns = roles (Procurement, Approver, Auditor, Staff, Supplier).
- Tick or untick each box: **V**iew, **C**reate, **E**dit, **D**elete, **A**pprove.
- **Save** applies immediately and is written to the audit trail.

Example: give Procurement Officers edit rights on Budgets, or grant the Auditor access to Reports only.

---

## 21. Settings

Admin / Super Admin. Three sections:

### 21.1 Organization Settings
- Name, email, phone, address, country, default currency (₦, $, €, £, GH₵, KSh) and tax ID.

### 21.2 Payment Gateways
- Connect **Paystack**, **Flutterwave** or **Mono** for subscription payments.
- Toggle each gateway **Active** and paste its **Public** and **Secret** keys from the provider dashboard.
- At checkout, the customer is redirected to the active provider to pay.
- A **demo gateway** is the fallback when no real gateway is configured (for testing).

### 21.3 Supplier Registration Requirements
- Define the documents suppliers must upload when registering.
- **Add Requirement** → document name, optional description, and a **Required** flag.
- Each requirement becomes its own upload field on the supplier registration form **and** a line on the supplier's **Compliance Checklist**.
- **Edit** name/description/required flag inline; **Delete** removes the requirement (existing uploads are kept).
- Defaults seeded for new organisations: CAC Certificate, TIN, VAT Registration, Audited Financial Statements, References & Past Performance, PENCOM & ITF Compliance.

---

## 22. Billing & Subscription

Manage the organisation's plan and payments.

### How it works

1. **Billing → Plan** shows current plan, features and pricing (Starter / Professional / Enterprise).
2. **Subscribe** → choose **Monthly** or **Yearly** billing.
3. **Checkout** redirects to the organisation's **active payment gateway** (Paystack, Flutterwave or Mono).
4. After payment, the gateway **callback/webhook** activates the subscription automatically.
5. **Invoices** and **Payments** tabs show the billing history.
6. **Cancel** or **Resume** the subscription as needed.

A fallback demo gateway is used for testing when no real gateway is configured.

---

## 23. Notifications & Profile

- **Notifications** (bell icon) — items needing your attention, e.g. new pending approvals. **Mark all as read** clears them.
- **Profile** — update your name, email and password.

---

## 24. End-to-End Workflows

### Procure-to-pay (goods/services)

```
Staff raise Request → Draft → Submit → Pending Approval
      → Approved via Workflow levels
      → Consolidated into Procurement Plan → Submit → Approved
      → Tender published → Suppliers bid → Bids evaluated
      → Winning bid recommended → Award Approved
      → Contract created → Activated → Milestones tracked
      → Purchase Order created → Approved → Issued → Goods Received
      → Supplier invoices → Verified → Matched (3-way) → Approved → Paid
      → Budget & Reports updated → everything in Audit Trail
```

### Onboarding a supplier

```
Admin configures Required Documents (Settings → Supplier Registration Requirements)
      → Supplier registers (one upload field per required document)
      → Approver reviews Compliance Checklist on the profile
      → Approve & Vet → supplier Approved
      → Supplier eligible for tenders
```

### Evaluating a tender (weighted)

```
Tender published → bids submitted
      → Open Evaluate → score each bid per criterion
      → weighted totals rank bids → Recommend Award
      → approver approves → contract created
```

---

## Quick Reference: Status Lifecycles

| Module | Lifecycle |
|--------|-----------|
| Procurement Request | Draft → Pending Approval → Approved / Rejected |
| Procurement Plan | Draft → Awaiting Approval → Approved / Rejected |
| Budget | Draft → Active → Closed |
| Supplier | Pending Vetting → Approved / Suspended / Blacklisted |
| Tender | Draft → Published → Closed → Under Evaluation → Awarded / Cancelled |
| Bid | Submitted → Evaluated → Awarded / Rejected / Withdrawn |
| Award | Recommended → Approved / Declined |
| Contract | Draft → Active → Completed / Terminated / Expired |
| Purchase Order | Draft → Approved → Issued → Partially Received → Received / Cancelled |
| Supplier Invoice | Pending → Verified → Matched → Approved → Paid / Rejected |
