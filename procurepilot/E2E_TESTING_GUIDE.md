# ProcurePilot — End-to-End (E2E) Testing Guide

A structured, role-based test guide for verifying the complete ProcurePilot system. Run each test case against a freshly seeded database and record PASS / FAIL in the result column. Every module is covered including the full procure-to-pay flow.

---

## Test Environment

| Item | Value |
|------|-------|
| Base URL | `http://localhost:8000` |
| Database | SQLite (`database/database.sqlite`) |
| Test users | See table below |
| Seed command | `php artisan db:seed --class=SupplierDocumentRequirementSeeder` then `php artisan migrate:fresh --seed` |
| Server | `php artisan serve --port=8000` |

### Test users (all password: `password`)

| Role | Email |
|------|-------|
| Super Admin | `super@nsc.gov.ng` |
| Admin | `admin@nsc.gov.ng` |
| Procurement Officer | `officer@nsc.gov.ng` |
| Approver (Tenders Board) | `tendersboard@nsc.gov.ng` |
| Auditor | `auditor@nsc.gov.ng` |
| Staff | `staff@nsc.gov.ng` |

---

## 1. Authentication & Access Control

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 1.1 | Valid login | Log in as `admin@nsc.gov.ng` / `password` | Redirected to dashboard (200) | ☐ |
| 1.2 | Invalid login | Wrong password | Error shown, no access | ☐ |
| 1.3 | Logout | Click logout | Returns to login page | ☐ |
| 1.4 | Super admin access | Log in as `super@nsc.gov.ng` | Sees **Role & Access** menu; can assign Super Admin role | ☐ |
| 1.5 | Admin cannot grant superadmin | As Admin, open Users → Add/Edit | "Super Admin" role option is hidden; API returns 403 | ☐ |
| 1.6 | Role permission filtering | As Staff, note sidebar items | Only Dashboard, Tenders, Requests, Categories visible | ☐ |
| 1.7 | Permission denial | As Auditor, open Settings | 403 (no access) | ☐ |

---

## 2. Dashboard

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 2.1 | Admin dashboard loads | Log in as Admin | KPI cards, charts, recent activity all render | ☐ |
| 2.2 | Monthly spend chart | Check **Monthly Procurement Spend** card | Real Chart.js interactive bar chart (12 months), values visible on hover | ☐ |
| 2.3 | Role-specific dashboard | Log in as Approver | Pending approvals panel shows only items the role can act on | ☐ |
| 2.4 | Pending approvals count | Compare nav badge vs list | Counts match | ☐ |

---

## 3. Supplier Registration & Document Requirements

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 3.1 | Admin configures requirements | Settings → Supplier Registration Requirements → Add | New requirement appears in the list | ☐ |
| 3.2 | Dashboard quick-add | Dashboard → "Add a Requirement" → submit | Requirement created (shown on dashboard + settings) | ☐ |
| 3.3 | Download requirements PDF | Click **Download PDF** on dashboard | Valid PDF downloads with org branding + full list | ☐ |
| 3.4 | Public PDF (client) | Visit `/supplier-requirements.pdf` logged out | PDF downloads, no login required | ☐ |
| 3.5 | Register supplier | Suppliers → Register → fill all fields | Supplier created with status **Pending Vetting** | ☐ |
| 3.6 | Upload per-requirement docs | Register supplier; upload a file in each **Required** field | Each file tagged to its requirement; uploads stored | ☐ |
| 3.7 | Required-field enforcement | Attempt submit with empty required doc | Browser blocks (required attribute) | ☐ |
| 3.8 | Compliance checklist | Open supplier profile | Shows each requirement met/missing + "X/Y required met" | ☐ |
| 3.9 | Edit adds uploads | Edit supplier → upload extra file under a requirement | File added; checklist count updates | ☐ |
| 3.10 | Approve supplier | As Approver, open supplier → Approve & Vet | Status → Approved; rating set | ☐ |

---

## 4. Procurement Requests

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 4.1 | Create request | As Staff/Procurement → New Request, add line items | Saved as Draft | ☐ |
| 4.2 | Submit request | Submit | Status → Pending Approval; approval records created per workflow | ☐ |
| 4.3 | Approve at level 1 | As Approver, approve (value within first level) | Level 1 approved; still pending at next level | ☐ |
| 4.4 | Approve at level 2 | As Admin, approve (value within second level) | Status → Approved | ☐ |
| 4.5 | Reject request | Submit new request, Reject | Status → Rejected with comment | ☐ |

---

## 5. Procurement Plans

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 5.1 | Create plan | Plans → New, add title/fiscal year | Saved as Draft | ☐ |
| 5.2 | Add plan items | Add items with category/cost/method | Items listed with totals | ☐ |
| 5.3 | Submit plan | Submit | Awaiting Approval | ☐ |
| 5.4 | Approve plan | As Approver, approve | Status → Approved | ☐ |
| 5.5 | Reject plan | Submit another, reject | Status → Rejected | ☐ |

---

## 6. Budgets

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 6.1 | Create budget | Budgets → New | Saved with allocated amounts | ☐ |
| 6.2 | Commit funds | Open budget → Commit | Committed amount increases | ☐ |
| 6.3 | Release funds | Open budget → Release | Committed amount decreases | ☐ |
| 6.4 | Budget visibility | As Auditor, open budgets | Read-only view (no create/edit) | ☐ |

---

## 7. Tenders

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 7.1 | Create tender | Tenders → New, fill details | Saved as Draft | ☐ |
| 7.2 | Add items | Add tender items | Line totals correct | ☐ |
| 7.3 | Add evaluation criteria | Add weighted criteria (total ≈ 100) | Criteria saved | ☐ |
| 7.4 | Publish tender | Publish | Status → Published; closing date in future | ☐ |
| 7.5 | Supplier sees open tender | Log in as Supplier, view tenders | Published tender visible | ☐ |
| 7.6 | Close tender | After closing date, close | Status → Closed | ☐ |
| 7.7 | Cancel tender | Create draft, cancel | Status → Cancelled | ☐ |

---

## 8. Bids & Evaluation

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 8.1 | Supplier submits bid | As Supplier, open published tender → submit itemised bid | Bid created as Submitted; total calculated | ☐ |
| 8.2 | Bid rejected when closed | Try bidding on closed/cancelled tender | Blocked (403/error) | ☐ |
| 8.3 | Score bids | As Officer, Evaluate → score each criterion | Weighted totals computed | ☐ |
| 8.4 | Recommend award | Click Recommend Award on top bid | Award created (Recommended) | ☐ |
| 8.5 | Withdraw bid | As Supplier, withdraw own bid | Status → Withdrawn | ☐ |

---

## 9. Awards

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 9.1 | Approve award | As Approver, approve recommended award | Status → Approved; tender → Awarded | ☐ |
| 9.2 | Decline award | Recommend another, decline | Status → Declined | ☐ |
| 9.3 | Create contract | From approved award → Create Contract | Contract created linked to award/supplier | ☐ |

---

## 10. Contracts

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 10.1 | Create contract | Contracts → New (from award) | Saved as Draft | ☐ |
| 10.2 | Upload contract document | Attach signed copy | Document uploads; can download | ☐ |
| 10.3 | Add milestone | Add milestone with due date/amount | Milestone listed as Pending | ☐ |
| 10.4 | Complete milestone | Mark milestone complete | Status → Complete | ☐ |
| 10.5 | Activate contract | Activate | Status → Active | ☐ |
| 10.6 | Complete contract | Complete | Status → Completed | ☐ |
| 10.7 | Terminate contract | Terminate | Status → Terminated | ☐ |

---

## 11. Purchase Orders

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 11.1 | Create PO | POs → New (from contract) | Saved as Draft | ☐ |
| 11.2 | Add PO items | Add items | Totals correct | ☐ |
| 11.3 | Approve PO | As Approver, approve | Status → Approved | ☐ |
| 11.4 | Issue PO | Issue | Status → Issued | ☐ |
| 11.5 | Receive goods (partial) | Receive with partial qty | Status → Partially Received | ☐ |
| 11.6 | Receive goods (full) | Receive full qty | Status → Received | ☐ |
| 11.7 | Cancel PO | Cancel a draft | Status → Cancelled | ☐ |

---

## 12. Supplier Invoices (3-Way Matching)

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 12.1 | Create invoice | Invoices → New, link to PO | Saved as Pending | ☐ |
| 12.2 | Verify invoice | Verify | Status → Verified | ☐ |
| 12.3 | Match invoice | Match against PO | Status → Matched (or Unmatched recorded) | ☐ |
| 12.4 | Approve invoice | Approve | Status → Approved | ☐ |
| 12.5 | Pay invoice | Pay | Status → Paid | ☐ |
| 12.6 | Reject invoice | Reject a new one | Status → Rejected | ☐ |

---

## 13. Reports & Compliance

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 13.1 | Run overview report | Reports → Overview | KPIs + data table render | ☐ |
| 13.2 | Filter report | Select type + filters | Results update | ☐ |
| 13.3 | Export report | Click Export | File downloads in chosen format | ☐ |
| 13.4 | Compliance dashboard | As Admin, open Compliance | Stats + framework + indicators render | ☐ |
| 13.5 | Compliance read-only | As Auditor, open Compliance | View only | ☐ |

---

## 14. Approval Workflow

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 14.1 | View workflow | Open Workflow | Departments, levels, pending, history render | ☐ |
| 14.2 | Add approval level | As Admin, add level with role + amount range | Level listed in sequence | ☐ |
| 14.3 | Amount routing | Submit a request within a range | Approval records only for matching levels | ☐ |

---

## 15. Users & Role Access

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 15.1 | Add user | Users → Add | User created | ☐ |
| 15.2 | Assign role | Edit user role | Role applied on next login | ☐ |
| 15.3 | Superadmin access matrix | As Super Admin → Role & Access | V/C/E/D/A matrix renders | ☐ |
| 15.4 | Update permissions | Toggle a permission, Save | Change immediate; audit record written | ☐ |
| 15.5 | Audit log | Open Audit Trail | All actions logged with user/timestamp | ☐ |

---

## 16. Settings

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 16.1 | Update organization | Settings → edit org fields → Save | Saved; shown on forms | ☐ |
| 16.2 | Configure gateway | Activate Paystack, enter keys → Save | Gateway active | ☐ |
| 16.3 | Requirement CRUD | Add / edit / delete a requirement | Reflected on supplier form + checklist | ☐ |
| 16.4 | Requirement toggle | Uncheck "Required" | Shows as Optional on forms | ☐ |

---

## 17. Billing & Subscription

| # | Test Case | Steps | Expected Result | Result |
|---|-----------|-------|-----------------|--------|
| 17.1 | View plans | Billing → Plan | Starter/Professional/Enterprise with pricing | ☐ |
| 17.2 | Subscribe (demo) | Subscribe with demo gateway | Redirected to demo checkout | ☐ |
| 17.3 | Subscribe (real gateway) | Subscribe; complete on provider | Callback activates subscription | ☐ |
| 17.4 | Webhook | POST provider webhook to `/billing/webhook/{gateway}` | Subscription updated server-side (CSRF-exempt) | ☐ |
| 17.5 | Invoices | Open billing invoices | Invoice list + detail render | ☐ |
| 17.6 | Cancel / resume | Cancel then resume | Status reflects both | ☐ |

---

## 18. End-to-End Procure-to-Pay Scenario

Run as a complete chain (this is the master test):

```
1. Admin configures required supplier documents (Settings + Dashboard quick-add)
2. Download requirements PDF → verify content
3. Officer registers a supplier, uploading all required documents
4. Approver vets and approves the supplier
5. Staff raises a Procurement Request → submits
6. Approver + Admin approve through workflow levels
7. Officer creates an approved Procurement Plan with the item
8. Officer publishes a Tender for the item
9. Supplier submits a bid
10. Officer evaluates (weighted scores) → recommends award
11. Approver approves the award
12. Officer creates + activates a Contract; adds milestones
13. Officer creates + approves + issues a PO
14. Officer records goods receipt (full)
15. Supplier invoice → verify → match → approve → pay
16. Verify spend appears in dashboard monthly chart + reports
17. Verify all steps present in Audit Trail
```

Expected: every step completes without error; statuses transition exactly as documented in the quick-reference table.

---

## Quick-Reference: Status Lifecycles

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

---

## Test Result Summary

| Section | Passed | Failed | Notes |
|---------|--------|--------|-------|
| 1. Authentication & Access | | | |
| 2. Dashboard | | | |
| 3. Suppliers & Documents | | | |
| 4. Requests | | | |
| 5. Plans | | | |
| 6. Budgets | | | |
| 7. Tenders | | | |
| 8. Bids & Evaluation | | | |
| 9. Awards | | | |
| 10. Contracts | | | |
| 11. Purchase Orders | | | |
| 12. Invoices | | | |
| 13. Reports & Compliance | | | |
| 14. Workflow | | | |
| 15. Users & Access | | | |
| 16. Settings | | | |
| 17. Billing | | | |
| 18. Procure-to-Pay E2E | | | |

**Overall: ___ / 80 test cases passed**

Signed (tester): ______________________ &nbsp;&nbsp;&nbsp; Date: ______________________
