<?php

namespace Database\Seeders;

use App\Models\Award;
use App\Models\ApprovalLevel;
use App\Models\ApprovalRecord;
use App\Models\Bid;
use App\Models\BidItem;
use App\Models\BidScore;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Contract;
use App\Models\ContractMilestone;
use App\Models\Department;
use App\Models\EvaluationCriterion;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\PoItem;
use App\Models\ProcurementPlan;
use App\Models\ProcurementPlanItem;
use App\Models\ProcurementRequest;
use App\Models\ProcurementRequestItem;
use App\Models\PurchaseOrder;
use App\Models\RolePermission;
use App\Models\Subscription;
use App\Models\Supplier;
use App\Models\SupplierDocumentRequirement;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceItem;
use App\Models\Tender;
use App\Models\TenderItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Award::query()->delete();
        BidScore::query()->delete();
        BidItem::query()->delete();
        Bid::query()->delete();
        ContractMilestone::query()->delete();
        Contract::query()->delete();
        TenderItem::query()->delete();
        EvaluationCriterion::query()->delete();
        Tender::query()->delete();
        ProcurementPlanItem::query()->delete();
        ProcurementPlan::query()->delete();
        PoItem::query()->delete();
        PurchaseOrder::query()->delete();
        SupplierInvoiceItem::query()->delete();
        SupplierInvoice::query()->delete();
        ProcurementRequestItem::query()->delete();
        ProcurementRequest::query()->delete();
        ApprovalRecord::query()->delete();
        Budget::query()->delete();
        Department::query()->delete();
        ApprovalLevel::query()->delete();
        Supplier::query()->delete();
        Category::query()->delete();
        Invoice::query()->delete();
        Subscription::query()->delete();
        User::query()->delete();
        Organization::query()->delete();

        $plans = [
            [
                'name' => 'Starter', 'slug' => 'starter', 'code' => 'STARTER',
                'description' => 'For small teams starting their procurement digitisation journey.',
                'price_monthly' => 15000, 'price_yearly' => 150000,
                'trial_days' => 14, 'is_popular' => false,
                'features' => ['Up to 5 users', '50 suppliers', '20 tenders / year', 'Standard reports', 'Email support'],
                'limits' => ['users' => 5, 'suppliers' => 50, 'tenders' => 20, 'storage_mb' => 1024],
            ],
            [
                'name' => 'Professional', 'slug' => 'professional', 'code' => 'PRO',
                'description' => 'For procurement units with full tendering, evaluation and contracts.',
                'price_monthly' => 45000, 'price_yearly' => 450000,
                'trial_days' => 14, 'is_popular' => true,
                'features' => ['Up to 20 users', 'Unlimited suppliers', 'Unlimited tenders', 'Weighted evaluation & scoring', 'Contracts & milestones', 'Audit trail & compliance', 'Priority support'],
                'limits' => ['users' => 20, 'suppliers' => 1000, 'tenders' => 1000, 'storage_mb' => 10240],
            ],
            [
                'name' => 'Enterprise', 'slug' => 'enterprise', 'code' => 'ENTERPRISE',
                'description' => 'For regulators and large organisations with multi-unit governance.',
                'price_monthly' => 150000, 'price_yearly' => 1500000,
                'trial_days' => 14, 'is_popular' => false,
                'features' => ['Unlimited users', 'Unlimited everything', 'Tenders Board workflow', 'Multi-department', 'API access', 'SSO / SAML', 'Dedicated account manager', '24/7 support'],
                'limits' => ['users' => 1000, 'suppliers' => 100000, 'tenders' => 100000, 'storage_mb' => 102400],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }

        $org = Organization::create([
            'name' => 'Nigerian Shippers\' Council',
            'slug' => 'nigerian-shippers-council',
            'email' => 'procurement@nsc.gov.ng',
            'phone' => '+234 1 460 3764',
            'address' => '4, Park Lane, Apapa, Lagos, Nigeria',
            'country' => 'Nigeria',
            'currency' => 'NGN',
            'tax_id' => 'RC 123456',
        ]);

        $admin = User::create([
            'name' => 'Adebayo Ogunlesi',
            'email' => 'admin@nsc.gov.ng',
            'password' => 'password',
            'organization_id' => $org->id,
            'role' => 'admin',
            'title' => 'Director, Procurement',
        ]);

        $officer = User::create([
            'name' => 'Funke Adeyemi',
            'email' => 'officer@nsc.gov.ng',
            'password' => 'password',
            'organization_id' => $org->id,
            'role' => 'procurement',
            'title' => 'Senior Procurement Officer',
        ]);

        $approver = User::create([
            'name' => 'Ibrahim Musa',
            'email' => 'tendersboard@nsc.gov.ng',
            'password' => 'password',
            'organization_id' => $org->id,
            'role' => 'approver',
            'title' => 'Tenders Board Chairman',
        ]);

        User::create([
            'name' => 'Ngozi Okonjo',
            'email' => 'auditor@nsc.gov.ng',
            'password' => 'password',
            'organization_id' => $org->id,
            'role' => 'auditor',
            'title' => 'Internal Audit',
        ]);

        $plan = Plan::where('slug', 'enterprise')->first();
        Subscription::create([
            'organization_id' => $org->id,
            'plan_id' => $plan->id,
            'status' => 'trialing',
            'billing_cycle' => 'monthly',
            'starts_at' => now(),
            'ends_at' => now()->addDays(14),
            'trial_ends_at' => now()->addDays(14),
        ]);

        // Default module access for non-admin roles (superadmin/admin bypass all checks)
        foreach (RolePermission::defaultGrants() as $role => $grants) {
            foreach ($grants as $module => $actions) {
                RolePermission::create([
                    'organization_id' => $org->id,
                    'role' => $role,
                    'module' => $module,
                    'can_view'    => str_contains($actions, 'v'),
                    'can_create'  => str_contains($actions, 'c'),
                    'can_edit'    => str_contains($actions, 'e'),
                    'can_delete'  => str_contains($actions, 'd'),
                    'can_approve' => str_contains($actions, 'a'),
                ]);
            }
        }

        // Default supplier registration document requirements
        $documentRequirements = [
            ['name' => 'Certificate of Incorporation (CAC)', 'description' => 'Certified true copy of the company registration certificate issued by the Corporate Affairs Commission.', 'is_required' => true],
            ['name' => 'CAC Form 2 & 7 (Particulars of Directors)', 'description' => 'Official CAC forms showing the particulars of the company directors.', 'is_required' => true],
            ['name' => 'Memorandum & Articles of Association', 'description' => 'Certified copies of the company\'s Memorandum and Articles of Association.', 'is_required' => true],
            ['name' => 'Tax Identification Number (TIN)', 'description' => 'Evidence of TIN registration with the Federal Inland Revenue Service.', 'is_required' => true],
            ['name' => 'Tax Clearance Certificate', 'description' => 'Current tax clearance certificate covering the last three years.', 'is_required' => true],
            ['name' => 'VAT Registration Certificate', 'description' => 'Proof of VAT registration with the Federal Inland Revenue Service.', 'is_required' => true],
            ['name' => 'Audited Financial Statements', 'description' => 'Audited accounts for the last two financial years, signed by a certified accountant.', 'is_required' => true],
            ['name' => 'PENCOM Compliance Certificate', 'description' => 'Evidence of compliance with the National Pension Commission (contributory pension).', 'is_required' => true],
            ['name' => 'ITF Compliance Certificate', 'description' => 'Industrial Training Fund (ITF) training contribution compliance certificate.', 'is_required' => true],
            ['name' => 'NSITF Compliance Certificate', 'description' => 'Nigeria Social Insurance Trust Fund (employees\' compensation) compliance certificate.', 'is_required' => true],
            ['name' => 'Bank Reference Letter', 'description' => 'Reference letter from the company\'s bank confirming account relationship and conduct.', 'is_required' => true],
            ['name' => 'References & Past Performance', 'description' => 'Letters of reference or proof of past contract performance from other clients.', 'is_required' => false],
            ['name' => 'Evidence of Relevant Licences / Permits', 'description' => 'Copies of any sector-specific licences, permits or certifications held by the company.', 'is_required' => false],
            ['name' => 'Insurance Cover (Public Liability)', 'description' => 'Certificate of insurance showing adequate public liability or professional indemnity cover.', 'is_required' => false],
        ];

        foreach ($documentRequirements as $idx => $req) {
            SupplierDocumentRequirement::create(array_merge($req, [
                'organization_id' => $org->id,
                'is_active' => true,
                'sort_order' => $idx,
            ]));
        }

        Invoice::create([
            'organization_id' => $org->id,
            'subscription_id' => $org->subscription->id,
            'number' => 'INV-2026-000001',
            'title' => 'Enterprise Plan — Monthly subscription',
            'amount' => 150000,
            'currency' => 'NGN',
            'status' => 'pending',
            'due_at' => now()->addDays(7),
        ]);

        $categories = [
            ['name' => 'Marine & Port Equipment', 'code' => 'MPE', 'type' => 'goods'],
            ['name' => 'Consulting Services', 'code' => 'CS', 'type' => 'services'],
            ['name' => 'ICT & Software', 'code' => 'ICT', 'type' => 'goods'],
            ['name' => 'Port Infrastructure Works', 'code' => 'PIW', 'type' => 'works'],
            ['name' => 'Office & General Supplies', 'code' => 'OGS', 'type' => 'goods'],
            ['name' => 'Transport & Logistics', 'code' => 'TL', 'type' => 'services'],
        ];

        foreach ($categories as $cat) {
            Category::create(array_merge($cat, ['organization_id' => $org->id]));
        }

        // Departments
        $departments = [
            ['name' => 'Marine & Port Services', 'code' => 'MPS', 'description' => 'Port operations and marine services.', 'manager_id' => $admin->id],
            ['name' => 'ICT & Digitalisation', 'code' => 'ICT', 'description' => 'Information technology and systems.', 'manager_id' => $admin->id],
            ['name' => 'Finance & Accounts', 'code' => 'FIN', 'description' => 'Budgeting, accounting and payments.', 'manager_id' => $admin->id],
            ['name' => 'Administration & General Services', 'code' => 'ADM', 'description' => 'Admin and general office services.', 'manager_id' => $admin->id],
            ['name' => 'Infrastructure & Works', 'code' => 'INF', 'description' => 'Port infrastructure and construction works.', 'manager_id' => $admin->id],
        ];

        foreach ($departments as $dept) {
            Department::create(array_merge($dept, ['organization_id' => $org->id]));
        }

        $marineDept = Department::where('code', 'MPS')->first();
        $ictDept = Department::where('code', 'ICT')->first();
        $finDept = Department::where('code', 'FIN')->first();
        $admDept = Department::where('code', 'ADM')->first();

        // Budgets
        Budget::create([
            'organization_id' => $org->id, 'name' => 'Operating Budget FY 2025/2026', 'fiscal_year' => '2025/2026',
            'department_id' => $admDept->id, 'category' => 'operating', 'allocated_amount' => 120000000,
            'committed_amount' => 82000000, 'spent_amount' => 41500000, 'currency' => 'NGN', 'status' => 'active',
        ]);
        Budget::create([
            'organization_id' => $org->id, 'name' => 'Capital Projects FY 2025/2026', 'fiscal_year' => '2025/2026',
            'department_id' => $marineDept->id, 'category' => 'capital', 'allocated_amount' => 650000000,
            'committed_amount' => 210000000, 'spent_amount' => 60000000, 'currency' => 'NGN', 'status' => 'active',
        ]);
        Budget::create([
            'organization_id' => $org->id, 'name' => 'ICT Modernisation', 'fiscal_year' => '2025/2026',
            'department_id' => $ictDept->id, 'category' => 'capital', 'allocated_amount' => 150000000,
            'committed_amount' => 80000000, 'spent_amount' => 15000000, 'currency' => 'NGN', 'status' => 'active',
        ]);
        Budget::create([
            'organization_id' => $org->id, 'name' => 'Consultancy Reserve', 'fiscal_year' => '2025/2026',
            'department_id' => $finDept->id, 'category' => 'operating', 'allocated_amount' => 40000000,
            'committed_amount' => 0, 'spent_amount' => 0, 'currency' => 'NGN', 'status' => 'draft',
        ]);

        // Approval levels (workflow configuration)
        ApprovalLevel::create(['organization_id' => $org->id, 'name' => 'Department Head', 'sequence' => 1, 'role' => 'approver', 'min_amount' => 0, 'max_amount' => 10000000, 'is_active' => true]);
        ApprovalLevel::create(['organization_id' => $org->id, 'name' => 'Procurement Director', 'sequence' => 2, 'role' => 'admin', 'min_amount' => 10000000, 'max_amount' => 50000000, 'is_active' => true]);
        ApprovalLevel::create(['organization_id' => $org->id, 'name' => 'Tenders Board', 'sequence' => 3, 'role' => 'approver', 'min_amount' => 50000000, 'max_amount' => null, 'is_active' => true]);

        // Procurement request (pending approval example)
        $pr1 = ProcurementRequest::create([
            'organization_id' => $org->id,
            'reference' => 'PR-1-2026-0001',
            'title' => 'Replacement of office desktops & UPS units',
            'justification' => 'Existing desktop fleet is beyond useful life and affects productivity across departments.',
            'department_id' => $ictDept->id,
            'requester_id' => $admin->id,
            'category_id' => Category::where('name', 'ICT & Software')->first()?->id,
            'budget_code' => 'ICT-CAP-2026-001',
            'required_date' => now()->addMonths(2),
            'priority' => 'high',
            'estimated_cost' => 12500000,
            'currency' => 'NGN',
            'status' => 'submitted',
        ]);
        ProcurementRequestItem::create(['procurement_request_id' => $pr1->id, 'description' => 'Desktop computers, i7, 16GB RAM, 512GB SSD', 'quantity' => 40, 'unit' => 'units', 'estimated_unit_cost' => 250000, 'estimated_total' => 10000000]);
        ProcurementRequestItem::create(['procurement_request_id' => $pr1->id, 'description' => 'UPS 1.5kVA online', 'quantity' => 10, 'unit' => 'units', 'estimated_unit_cost' => 250000, 'estimated_total' => 2500000]);

        // Pending approval records for the submitted request (12.5M -> Department Head + Procurement Director)
        $pr1Levels = ApprovalLevel::where('organization_id', $org->id)->where('min_amount', '<=', 12500000)->orderBy('sequence')->get();
        foreach ($pr1Levels as $pr1Level) {
            ApprovalRecord::create([
                'organization_id' => $org->id,
                'approvable_type' => ProcurementRequest::class,
                'approvable_id' => $pr1->id,
                'approval_level_id' => $pr1Level->id,
                'status' => 'pending',
            ]);
        }

        $pr2 = ProcurementRequest::create([
            'organization_id' => $org->id,
            'reference' => 'PR-1-2026-0002',
            'title' => 'Cargo scanning equipment calibration services',
            'justification' => 'Annual calibration of cargo scanners to maintain port safety compliance.',
            'department_id' => $marineDept->id,
            'requester_id' => $officer->id,
            'category_id' => Category::where('name', 'Consulting Services')->first()?->id,
            'budget_code' => 'MPS-OP-2026-014',
            'required_date' => now()->addMonth(),
            'priority' => 'critical',
            'estimated_cost' => 18500000,
            'currency' => 'NGN',
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now()->subDays(2),
        ]);

        // Approved approval records for the approved request (18.5M -> Department Head + Procurement Director)
        $pr2Levels = ApprovalLevel::where('organization_id', $org->id)->where('min_amount', '<=', 18500000)->orderBy('sequence')->get();
        foreach ($pr2Levels as $pr2Level) {
            ApprovalRecord::create([
                'organization_id' => $org->id,
                'approvable_type' => ProcurementRequest::class,
                'approvable_id' => $pr2->id,
                'approval_level_id' => $pr2Level->id,
                'status' => 'approved',
                'approver_id' => $approver->id,
                'comment' => 'Approved in line with the annual calibration schedule.',
                'decided_at' => now()->subDays(2),
            ]);
        }

        $suppliers = [
            ['name' => 'APM Terminals Nigeria Ltd', 'reg_number' => 'RC 482195', 'category' => 'Port Infrastructure Works', 'status' => 'approved'],
            ['name' => 'Dangote Port Operations', 'reg_number' => 'RC 26034', 'category' => 'Transport & Logistics', 'status' => 'approved'],
            ['name' => 'Sifani Ship Chandlers', 'reg_number' => 'RC 1239001', 'category' => 'Marine & Port Equipment', 'status' => 'approved'],
            ['name' => 'Zinox Technologies Ltd', 'reg_number' => 'RC 371284', 'category' => 'ICT & Software', 'status' => 'approved'],
            ['name' => 'Julius Berger Nigeria Plc', 'reg_number' => 'RC 2973', 'category' => 'Port Infrastructure Works', 'status' => 'approved'],
            ['name' => 'Greenland Marine Logistics', 'reg_number' => 'RC 771245', 'category' => 'Transport & Logistics', 'status' => 'approved'],
            ['name' => 'Hamdalah Freight Forwarders', 'reg_number' => 'RC 998123', 'category' => 'Transport & Logistics', 'status' => 'pending'],
        ];

        foreach ($suppliers as $sup) {
            $category = Category::where('name', $sup['category'])->first();
            Supplier::create([
                'organization_id' => $org->id,
                'name' => $sup['name'],
                'reg_number' => $sup['reg_number'],
                'email' => strtolower(str_replace([' ', 'Ltd', 'Plc', 'Nigeria'], ['', '', '', ''], $sup['name'])) . '@example.com',
                'phone' => '+234 800 000 0000',
                'address' => 'Lagos, Nigeria',
                'country' => 'Nigeria',
                'category_id' => $category?->id,
                'tax_id' => 'TIN-' . substr(md5($sup['name']), 0, 9),
                'bank_name' => 'GTBank',
                'bank_account_name' => $sup['name'],
                'bank_account_number' => '0012345678',
                'certifications' => 'ISO 9001:2015, ITF Compliance',
                'status' => $sup['status'],
                'approved_by' => $sup['status'] === 'approved' ? $admin->id : null,
                'approved_at' => $sup['status'] === 'approved' ? now()->subDays(30) : null,
                'rating' => $sup['status'] === 'approved' ? rand(70, 95) : 0,
            ]);
        }

        $plan2025 = ProcurementPlan::create([
            'organization_id' => $org->id,
            'title' => 'Annual Procurement Plan FY 2025/2026',
            'fiscal_year' => '2025/2026',
            'description' => 'Consolidated procurement plan approved by the Tenders Board per PPA 2007.',
            'status' => 'approved',
            'created_by' => $officer->id,
            'approved_by' => $approver->id,
            'approved_at' => now()->subMonths(3),
        ]);

        $planItems = [
            ['Supply of marine diesel for port operations', 'Marine & Port Equipment', 25000000, 4, 'open_competitive', 'critical'],
            ['Port security surveillance system upgrade', 'ICT & Software', 80000000, 1, 'open_competitive', 'high'],
            ['Consultancy for port tariff review', 'Consulting Services', 15000000, 1, 'restricted', 'high'],
            ['Road rehabilitation at Tin Can Island Port', 'Port Infrastructure Works', 450000000, 1, 'open_competitive', 'critical'],
            ['Office consumables & furniture', 'Office & General Supplies', 5000000, 2, 'open_competitive', 'normal'],
        ];

        foreach ($planItems as $item) {
            $category = Category::where('name', $item[1])->first();
            ProcurementPlanItem::create([
                'procurement_plan_id' => $plan2025->id,
                'title' => $item[0],
                'description' => 'Procurement under annual plan.',
                'category_id' => $category?->id,
                'estimated_cost' => $item[2],
                'quantity' => $item[3],
                'method' => $item[4],
                'priority' => $item[5],
                'expected_date' => now()->addMonths($item[3]),
                'status' => 'planned',
            ]);
        }

        // Tender 1: Port surveillance system
        $tender1 = Tender::create([
            'organization_id' => $org->id,
            'reference' => 'RFQ-NSC-2026-0001',
            'title' => 'Supply and Installation of Port Security Surveillance System',
            'description' => 'Design, supply and installation of CCTV surveillance system covering berth areas, gate houses and administrative buildings at the ports.',
            'category_id' => Category::where('name', 'ICT & Software')->first()?->id,
            'type' => 'open',
            'method' => 'open_competitive',
            'budget' => 80000000,
            'currency' => 'NGN',
            'published_at' => now()->subDays(20),
            'closing_at' => now()->addDays(10),
            'opening_at' => now()->addDays(12),
            'evaluation_method' => 'weighted_score',
            'status' => 'published',
            'created_by' => $officer->id,
        ]);

        TenderItem::create(['tender_id' => $tender1->id, 'description' => '4K IP CCTV cameras with analytics', 'quantity' => 120, 'unit' => 'units', 'estimated_unit_price' => 250000]);
        TenderItem::create(['tender_id' => $tender1->id, 'description' => 'Network video recorder (NVR)', 'quantity' => 8, 'unit' => 'units', 'estimated_unit_price' => 1500000]);
        TenderItem::create(['tender_id' => $tender1->id, 'description' => 'Installation, cabling & commissioning', 'quantity' => 1, 'unit' => 'lump sum', 'estimated_unit_price' => 15000000]);

        $criteria1 = [
            ['name' => 'Technical Capability', 'weight' => 30, 'max_score' => 100],
            ['name' => 'Experience & Track Record', 'weight' => 25, 'max_score' => 100],
            ['name' => 'Price Competitiveness', 'weight' => 30, 'max_score' => 100],
            ['name' => 'Local Content & Compliance', 'weight' => 15, 'max_score' => 100],
        ];
        foreach ($criteria1 as $c) {
            EvaluationCriterion::create(array_merge($c, ['tender_id' => $tender1->id]));
        }

        // Tender 2: Marine diesel
        $tender2 = Tender::create([
            'organization_id' => $org->id,
            'reference' => 'RFQ-NSC-2026-0002',
            'title' => 'Supply of Marine Diesel Oil (MDO) for Port Operations',
            'description' => 'Periodic supply of marine diesel oil to power port equipment and standby generators.',
            'category_id' => Category::where('name', 'Marine & Port Equipment')->first()?->id,
            'type' => 'open',
            'method' => 'open_competitive',
            'budget' => 25000000,
            'currency' => 'NGN',
            'published_at' => now()->subDays(30),
            'closing_at' => now()->subDays(2),
            'opening_at' => now()->subDays(1),
            'evaluation_method' => 'lowest_price',
            'status' => 'closed',
            'created_by' => $officer->id,
        ]);

        TenderItem::create(['tender_id' => $tender2->id, 'description' => 'Marine diesel oil (MDO)', 'quantity' => 50000, 'unit' => 'litres', 'estimated_unit_price' => 420]);
        TenderItem::create(['tender_id' => $tender2->id, 'description' => 'Bulk delivery & storage supervision', 'quantity' => 1, 'unit' => 'lump sum', 'estimated_unit_price' => 2000000]);

        $criteria2 = [
            ['name' => 'Technical & Quality Compliance', 'weight' => 40, 'max_score' => 100],
            ['name' => 'Delivery Capability', 'weight' => 20, 'max_score' => 100],
            ['name' => 'Price', 'weight' => 40, 'max_score' => 100],
        ];
        foreach ($criteria2 as $c) {
            EvaluationCriterion::create(array_merge($c, ['tender_id' => $tender2->id]));
        }

        // Bids for tender 2
        $supplierSifani = Supplier::where('name', 'Sifani Ship Chandlers')->first();
        $supplierGreenland = Supplier::where('name', 'Greenland Marine Logistics')->first();
        $supplierDangote = Supplier::where('name', 'Dangote Port Operations')->first();

        $bid1 = Bid::create([
            'organization_id' => $org->id, 'tender_id' => $tender2->id, 'supplier_id' => $supplierDangote->id,
            'reference' => 'BID-NSC-2026-0001', 'total_amount' => 23500000, 'currency' => 'NGN',
            'compliance_declaration' => 'Complies with PPA 2007 and all relevant regulations.',
            'status' => 'evaluated', 'submitted_at' => now()->subDays(5),
            'technical_score' => 78.5, 'evaluated_by' => $officer->id, 'evaluated_at' => now()->subDays(2),
        ]);
        BidItem::create(['bid_id' => $bid1->id, 'description' => 'Marine diesel oil (MDO)', 'quantity' => 50000, 'unit' => 'litres', 'unit_price' => 430, 'total_price' => 21500000]);
        BidItem::create(['bid_id' => $bid1->id, 'description' => 'Bulk delivery & storage supervision', 'quantity' => 1, 'unit' => 'lump sum', 'unit_price' => 2000000, 'total_price' => 2000000]);

        $bid2 = Bid::create([
            'organization_id' => $org->id, 'tender_id' => $tender2->id, 'supplier_id' => $supplierGreenland->id,
            'reference' => 'BID-NSC-2026-0002', 'total_amount' => 22500000, 'currency' => 'NGN',
            'compliance_declaration' => 'Fully compliant. All taxes and duties inclusive.',
            'status' => 'evaluated', 'submitted_at' => now()->subDays(4),
            'technical_score' => 82.0, 'evaluated_by' => $officer->id, 'evaluated_at' => now()->subDays(2),
        ]);
        BidItem::create(['bid_id' => $bid2->id, 'description' => 'Marine diesel oil (MDO)', 'quantity' => 50000, 'unit' => 'litres', 'unit_price' => 410, 'total_price' => 20500000]);
        BidItem::create(['bid_id' => $bid2->id, 'description' => 'Bulk delivery & storage supervision', 'quantity' => 1, 'unit' => 'lump sum', 'unit_price' => 2000000, 'total_price' => 2000000]);

        $bid3 = Bid::create([
            'organization_id' => $org->id, 'tender_id' => $tender2->id, 'supplier_id' => $supplierSifani->id,
            'reference' => 'BID-NSC-2026-0003', 'total_amount' => 24300000, 'currency' => 'NGN',
            'compliance_declaration' => 'Compliant with technical specifications.',
            'status' => 'evaluated', 'submitted_at' => now()->subDays(3),
            'technical_score' => 75.0, 'evaluated_by' => $officer->id, 'evaluated_at' => now()->subDays(1),
        ]);
        BidItem::create(['bid_id' => $bid3->id, 'description' => 'Marine diesel oil (MDO)', 'quantity' => 50000, 'unit' => 'litres', 'unit_price' => 446, 'total_price' => 22300000]);
        BidItem::create(['bid_id' => $bid3->id, 'description' => 'Bulk delivery & storage supervision', 'quantity' => 1, 'unit' => 'lump sum', 'unit_price' => 2000000, 'total_price' => 2000000]);

        $criteriaT2 = $tender2->criteria;
        foreach ([$bid1, $bid2, $bid3] as $idx => $bid) {
            foreach ($criteriaT2 as $crit) {
                BidScore::create([
                    'bid_id' => $bid->id,
                    'criterion_id' => $crit->id,
                    'evaluator_id' => $officer->id,
                    'score' => [75, 85, 80][$idx % 3] + $crit->id,
                    'comment' => 'Satisfactory response.',
                ]);
            }
        }

        // Award for tender 2 (approved)
        $award1 = Award::create([
            'organization_id' => $org->id,
            'tender_id' => $tender2->id,
            'bid_id' => $bid2->id,
            'supplier_id' => $supplierGreenland->id,
            'award_amount' => 22500000,
            'currency' => 'NGN',
            'justification' => 'Lowest evaluated responsive bid with strong technical capability.',
            'status' => 'approved',
            'decided_by' => $approver->id,
            'decided_at' => now()->subDays(1),
        ]);
        $bid2->update(['status' => 'awarded']);
        $bid1->update(['status' => 'evaluated']);
        $bid3->update(['status' => 'evaluated']);
        $tender2->update(['status' => 'awarded', 'award_notice' => 'Awarded to Greenland Marine Logistics for ₦22,500,000.00 NGN', 'approved_by' => $approver->id, 'approved_at' => now()->subDays(1)]);

        // Contract from award
        $contract1 = Contract::create([
            'organization_id' => $org->id,
            'reference' => 'CON-NSC-2026-0001',
            'title' => 'Supply of Marine Diesel Oil (MDO) — Greenland Marine Logistics',
            'description' => 'Periodic supply of marine diesel oil to port operations for one year.',
            'supplier_id' => $supplierGreenland->id,
            'tender_id' => $tender2->id,
            'award_id' => $award1->id,
            'value' => 22500000,
            'currency' => 'NGN',
            'start_date' => now()->addDays(1),
            'end_date' => now()->addYear(),
            'payment_terms' => 'Net 30',
            'status' => 'active',
            'created_by' => $officer->id,
            'signed_at' => now(),
        ]);

        ContractMilestone::create(['contract_id' => $contract1->id, 'title' => 'Initial 25,000 litres delivery', 'due_date' => now()->addMonth(), 'amount' => 10750000, 'status' => 'pending']);
        ContractMilestone::create(['contract_id' => $contract1->id, 'title' => 'Final delivery & handover', 'due_date' => now()->addMonths(2), 'amount' => 11750000, 'status' => 'pending']);

        // PO against contract
        $po1 = PurchaseOrder::create([
            'organization_id' => $org->id,
            'reference' => 'PO-NSC-2026-0001',
            'title' => 'MDO Supply — Batch 1',
            'description' => 'First delivery of marine diesel oil under contract.',
            'supplier_id' => $supplierGreenland->id,
            'contract_id' => $contract1->id,
            'tender_id' => $tender2->id,
            'order_date' => now(),
            'expected_delivery' => now()->addWeeks(2),
            'total' => 10750000,
            'currency' => 'NGN',
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);
        PoItem::create(['purchase_order_id' => $po1->id, 'description' => 'Marine diesel oil (MDO)', 'quantity' => 25000, 'unit' => 'litres', 'unit_price' => 410, 'total_price' => 10250000, 'received_qty' => 0]);
        PoItem::create(['purchase_order_id' => $po1->id, 'description' => 'Delivery & supervision', 'quantity' => 1, 'unit' => 'lump sum', 'unit_price' => 500000, 'total_price' => 500000, 'received_qty' => 0]);

        // A recommended award for the open tender 1 - create bids
        $supplierZinox = Supplier::where('name', 'Zinox Technologies Ltd')->first();
        $supplierJulius = Supplier::where('name', 'Julius Berger Nigeria Plc')->first();

        $bid4 = Bid::create([
            'organization_id' => $org->id, 'tender_id' => $tender1->id, 'supplier_id' => $supplierZinox->id,
            'reference' => 'BID-NSC-2026-0004', 'total_amount' => 76000000, 'currency' => 'NGN',
            'compliance_declaration' => 'Compliant with specifications and PPA 2007.',
            'status' => 'evaluated', 'submitted_at' => now()->subDays(3),
            'technical_score' => 88.0, 'evaluated_by' => $officer->id, 'evaluated_at' => now(),
        ]);
        BidItem::create(['bid_id' => $bid4->id, 'description' => '4K IP CCTV cameras with analytics', 'quantity' => 120, 'unit' => 'units', 'unit_price' => 240000, 'total_price' => 28800000]);
        BidItem::create(['bid_id' => $bid4->id, 'description' => 'Network video recorder (NVR)', 'quantity' => 8, 'unit' => 'units', 'unit_price' => 1400000, 'total_price' => 11200000]);
        BidItem::create(['bid_id' => $bid4->id, 'description' => 'Installation, cabling & commissioning', 'quantity' => 1, 'unit' => 'lump sum', 'unit_price' => 36000000, 'total_price' => 36000000]);

        $bid5 = Bid::create([
            'organization_id' => $org->id, 'tender_id' => $tender1->id, 'supplier_id' => $supplierJulius->id,
            'reference' => 'BID-NSC-2026-0005', 'total_amount' => 79500000, 'currency' => 'NGN',
            'compliance_declaration' => 'Fully compliant.',
            'status' => 'evaluated', 'submitted_at' => now()->subDays(2),
            'technical_score' => 84.0, 'evaluated_by' => $officer->id, 'evaluated_at' => now(),
        ]);
        BidItem::create(['bid_id' => $bid5->id, 'description' => '4K IP CCTV cameras with analytics', 'quantity' => 120, 'unit' => 'units', 'unit_price' => 250000, 'total_price' => 30000000]);
        BidItem::create(['bid_id' => $bid5->id, 'description' => 'Network video recorder (NVR)', 'quantity' => 8, 'unit' => 'units', 'unit_price' => 1500000, 'total_price' => 12000000]);
        BidItem::create(['bid_id' => $bid5->id, 'description' => 'Installation, cabling & commissioning', 'quantity' => 1, 'unit' => 'lump sum', 'unit_price' => 37500000, 'total_price' => 37500000]);

        foreach ($tender1->criteria as $idx => $crit) {
            foreach ([$bid4, $bid5] as $bid) {
                BidScore::create([
                    'bid_id' => $bid->id,
                    'criterion_id' => $crit->id,
                    'evaluator_id' => $officer->id,
                    'score' => $bid->id === $bid4->id ? [90, 85, 80, 95][$idx % 4] : [85, 80, 78, 90][$idx % 4],
                    'comment' => 'Evaluated per criteria.',
                ]);
            }
        }

        $award2 = Award::create([
            'organization_id' => $org->id,
            'tender_id' => $tender1->id,
            'bid_id' => $bid4->id,
            'supplier_id' => $supplierZinox->id,
            'award_amount' => 76000000,
            'currency' => 'NGN',
            'justification' => 'Highest technical score with competitive pricing and strong local content.',
            'status' => 'recommended',
            'decided_by' => $officer->id,
            'decided_at' => now(),
        ]);

        // Supplier invoice linked to PO 1 (for three-way matching)
        $sinv1 = SupplierInvoice::create([
            'organization_id' => $org->id,
            'number' => 'SINV-2026-0001',
            'supplier_id' => $supplierGreenland->id,
            'purchase_order_id' => $po1->id,
            'contract_id' => $contract1->id,
            'invoice_date' => now()->subDays(5),
            'due_date' => now()->addDays(25),
            'subtotal' => 10250000,
            'tax_amount' => 0,
            'total' => 10250000,
            'currency' => 'NGN',
            'status' => 'verified',
            'match_status' => 'unmatched',
            'notes' => 'Supplier invoice for MDO batch 1.',
            'verified_by' => $officer->id,
            'verified_at' => now()->subDays(2),
        ]);
        SupplierInvoiceItem::create(['supplier_invoice_id' => $sinv1->id, 'po_item_id' => $po1->items[0]->id, 'description' => 'Marine diesel oil (MDO)', 'quantity' => 25000, 'unit' => 'litres', 'unit_price' => 410, 'total_price' => 10250000]);

        $this->command->info('Demo data seeded successfully.');
    }
}
