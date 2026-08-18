<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\AuditLog;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Contract;
use App\Models\Department;
use App\Models\GoodsReceipt;
use App\Models\Payment;
use App\Models\ProcurementRequest;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\Tender;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public const TYPES = [
        'overview'      => 'Overview',
        'requests'      => 'Procurement Requests',
        'tenders'       => 'Tenders',
        'contracts'     => 'Contracts',
        'purchase-orders' => 'Purchase Orders',
        'invoices'      => 'Invoices',
        'payments'      => 'Payments',
        'budgets'       => 'Budgets',
        'suppliers'     => 'Suppliers',
        'receipts'      => 'Goods Receipts',
    ];

    public function index(Request $request)
    {
        $org = currentOrganization();
        $filters = $this->normalizeFilters($request);
        $type = $filters['type'];

        $data = $this->buildReport($type, $filters, $org->id);

        $recentAudits = AuditLog::where('organization_id', $org->id)
            ->with('user')
            ->latest()
            ->take(8)
            ->get();

        $kpi = $this->kpis($org->id, $filters);

        $reportTypes = self::TYPES;
        $categories = Category::where('organization_id', $org->id)->orderBy('name')->get();
        $suppliers = Supplier::where('organization_id', $org->id)->orderBy('name')->get();
        $departments = Department::where('organization_id', $org->id)->orderBy('name')->get();
        $statuses = $this->statusOptions($type);

        return view('reports.index', compact(
            'kpi', 'data', 'filters', 'reportTypes', 'categories', 'suppliers', 'departments', 'statuses', 'recentAudits'
        ));
    }

    public function export(Request $request, string $format)
    {
        $org = currentOrganization();
        $filters = $this->normalizeFilters($request);
        $type = $filters['type'];
        $data = $this->buildReport($type, $filters, $org->id);

        $filename = 'report-' . $type . '-' . now()->format('Ymd-His');

        switch ($format) {
            case 'excel':
                return Excel::download(new ReportExport($data['columns'], $data['rows'], $data['title']), $filename . '.xlsx');
            case 'csv':
                return Excel::download(new ReportExport($data['columns'], $data['rows'], $data['title']), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
            case 'pdf':
                $pdf = Pdf::loadView('reports.pdf', compact('data', 'filters', 'org'))
                    ->setPaper('a4', 'landscape');
                return $pdf->download($filename . '.pdf');
        }

        abort(400, 'Unsupported export format.');
    }

    private function normalizeFilters(Request $request): array
    {
        $allowed = array_keys(self::TYPES);

        return [
            'type'         => in_array($request->query('type'), $allowed, true) ? $request->query('type') : 'overview',
            'date_from'    => $request->query('date_from') ?: null,
            'date_to'      => $request->query('date_to') ?: null,
            'status'       => $request->query('status') ?: null,
            'category_id'  => $request->query('category_id') ?: null,
            'supplier_id'  => $request->query('supplier_id') ?: null,
            'department_id'=> $request->query('department_id') ?: null,
            'fiscal_year'  => $request->query('fiscal_year') ?: null,
        ];
    }

    private function applyDateRange($query, string $column, array $filters): void
    {
        if ($filters['date_from']) {
            $query->whereDate($column, '>=', $filters['date_from']);
        }
        if ($filters['date_to']) {
            $query->whereDate($column, '<=', $filters['date_to']);
        }
    }

    private function kpis(int $orgId, array $filters): array
    {
        $orgQ = fn ($model) => $model::where('organization_id', $orgId);

        $contracts = (clone $orgQ(Contract::class));
        $this->applyDateRange($contracts, 'start_date', $filters);

        $invoices = (clone $orgQ(SupplierInvoice::class));
        $this->applyDateRange($invoices, 'invoice_date', $filters);

        $requests = (clone $orgQ(ProcurementRequest::class));
        $this->applyDateRange($requests, 'created_at', $filters);

        return [
            'requests'       => (clone $requests)->count(),
            'requests_value' => (clone $requests)->sum('estimated_cost'),
            'contracts'      => (clone $contracts)->count(),
            'contract_value' => (clone $contracts)->where('status', 'active')->sum('value'),
            'pos'            => PurchaseOrder::where('organization_id', $orgId)->count(),
            'po_value'       => PurchaseOrder::where('organization_id', $orgId)->sum('total'),
            'invoices'       => (clone $invoices)->count(),
            'paid'           => (clone $invoices)->where('status', 'paid')->sum('total'),
            'suppliers'      => Supplier::where('organization_id', $orgId)->where('status', 'approved')->count(),
            'receipts'       => GoodsReceipt::where('organization_id', $orgId)->count(),
            'budgets'        => Budget::where('organization_id', $orgId)->count(),
        ];
    }

    private function buildReport(string $type, array $filters, int $orgId): array
    {
        return match ($type) {
            'requests'       => $this->requestsReport($filters, $orgId),
            'tenders'        => $this->tendersReport($filters, $orgId),
            'contracts'      => $this->contractsReport($filters, $orgId),
            'purchase-orders'=> $this->purchaseOrdersReport($filters, $orgId),
            'invoices'       => $this->invoicesReport($filters, $orgId),
            'payments'       => $this->paymentsReport($filters, $orgId),
            'budgets'        => $this->budgetsReport($filters, $orgId),
            'suppliers'      => $this->suppliersReport($filters, $orgId),
            'receipts'       => $this->receiptsReport($filters, $orgId),
            default          => $this->overviewReport($filters, $orgId),
        };
    }

    private function overviewReport(array $filters, int $orgId): array
    {
        $spendByCategory = Contract::where('contracts.organization_id', $orgId)
            ->selectRaw('COALESCE(tenders.category_id, 0) as category_id, SUM(contracts.value) as total')
            ->leftJoin('tenders', 'contracts.tender_id', '=', 'tenders.id')
            ->groupBy('category_id')
            ->get()
            ->map(function ($row) {
                $category = $row->category_id ? Category::find($row->category_id) : null;
                return [
                    'name'  => $category?->name ?? 'Uncategorised',
                    'total' => (float) $row->total,
                ];
            })
            ->filter(fn ($r) => $r['total'] > 0)
            ->sortByDesc('total')
            ->values();

        $statusSummary = [
            'tenders'   => Tender::where('organization_id', $orgId)->selectRaw('status, COUNT(*) as count')->groupBy('status')->get(),
            'contracts' => Contract::where('organization_id', $orgId)->selectRaw('status, COUNT(*) as count')->groupBy('status')->get(),
            'requests'  => ProcurementRequest::where('organization_id', $orgId)->selectRaw('status, COUNT(*) as count')->groupBy('status')->get(),
        ];

        $budgetSummary = [
            'allocated' => Budget::where('organization_id', $orgId)->sum('allocated_amount'),
            'committed' => Budget::where('organization_id', $orgId)->sum('committed_amount'),
            'spent'     => Budget::where('organization_id', $orgId)->sum('spent_amount'),
        ];

        return [
            'title'    => 'Overview',
            'columns'  => ['Metric', 'Value'],
            'rows'     => $this->overviewRows($filters, $orgId),
            'extra'    => compact('spendByCategory', 'statusSummary', 'budgetSummary'),
        ];
    }

    private function overviewRows(array $filters, int $orgId): array
    {
        $kpi = $this->kpis($orgId, $filters);

        return [
            ['Procurement requests', number_format($kpi['requests'])],
            ['Requests estimated value', '₦ ' . number_format($kpi['requests_value'], 0)],
            ['Active contracts', number_format($kpi['contracts'])],
            ['Active contract value', '₦ ' . number_format($kpi['contract_value'], 0)],
            ['Purchase orders', number_format($kpi['pos'])],
            ['PO value', '₦ ' . number_format($kpi['po_value'], 0)],
            ['Invoices', number_format($kpi['invoices'])],
            ['Invoices paid', '₦ ' . number_format($kpi['paid'], 0)],
            ['Approved suppliers', number_format($kpi['suppliers'])],
            ['Goods receipts', number_format($kpi['receipts'])],
            ['Budgets', number_format($kpi['budgets'])],
        ];
    }

    private function requestsReport(array $filters, int $orgId): array
    {
        $q = ProcurementRequest::with(['department', 'category', 'requester'])
            ->where('procurement_requests.organization_id', $orgId);

        $this->applyDateRange($q, 'procurement_requests.created_at', $filters);

        if ($filters['status']) {
            $q->where('procurement_requests.status', $filters['status']);
        }
        if ($filters['category_id']) {
            $q->where('procurement_requests.category_id', $filters['category_id']);
        }
        if ($filters['department_id']) {
            $q->where('procurement_requests.department_id', $filters['department_id']);
        }

        $rows = $q->orderByDesc('procurement_requests.created_at')->get()->map(fn ($r) => [
            $r->reference,
            $r->title,
            $r->department?->name ?? '—',
            $r->category?->name ?? '—',
            number_format((float) $r->estimated_cost, 2),
            ucfirst(str_replace('_', ' ', $r->status)),
            $r->created_at->format('d M Y'),
        ])->all();

        return [
            'title'   => 'Procurement Requests',
            'columns' => ['Reference', 'Title', 'Department', 'Category', 'Estimated Cost', 'Status', 'Created'],
            'rows'    => $rows,
        ];
    }

    private function tendersReport(array $filters, int $orgId): array
    {
        $q = Tender::with('category')->where('tenders.organization_id', $orgId);

        $this->applyDateRange($q, 'tenders.created_at', $filters);

        if ($filters['status']) {
            $q->where('tenders.status', $filters['status']);
        }
        if ($filters['category_id']) {
            $q->where('tenders.category_id', $filters['category_id']);
        }

        $rows = $q->orderByDesc('tenders.created_at')->get()->map(fn ($t) => [
            $t->reference,
            $t->title,
            $t->category?->name ?? '—',
            ucfirst(str_replace('_', ' ', $t->method)),
            number_format((float) $t->budget, 2),
            ucfirst(str_replace('_', ' ', $t->status)),
            $t->closing_at?->format('d M Y') ?? '—',
            $t->created_at->format('d M Y'),
        ])->all();

        return [
            'title'   => 'Tenders',
            'columns' => ['Reference', 'Title', 'Category', 'Method', 'Budget', 'Status', 'Closing', 'Created'],
            'rows'    => $rows,
        ];
    }

    private function contractsReport(array $filters, int $orgId): array
    {
        $q = Contract::with('supplier')->where('contracts.organization_id', $orgId);

        $this->applyDateRange($q, 'contracts.start_date', $filters);

        if ($filters['status']) {
            $q->where('contracts.status', $filters['status']);
        }
        if ($filters['supplier_id']) {
            $q->where('contracts.supplier_id', $filters['supplier_id']);
        }

        $rows = $q->orderByDesc('contracts.created_at')->get()->map(fn ($c) => [
            $c->reference,
            $c->title,
            $c->supplier?->name ?? '—',
            number_format((float) $c->value, 2),
            $c->start_date?->format('d M Y') ?? '—',
            $c->end_date?->format('d M Y') ?? '—',
            ucfirst(str_replace('_', ' ', $c->status)),
        ])->all();

        return [
            'title'   => 'Contracts',
            'columns' => ['Reference', 'Title', 'Supplier', 'Value', 'Start', 'End', 'Status'],
            'rows'    => $rows,
        ];
    }

    private function purchaseOrdersReport(array $filters, int $orgId): array
    {
        $q = PurchaseOrder::with('supplier')->where('purchase_orders.organization_id', $orgId);

        $this->applyDateRange($q, 'purchase_orders.created_at', $filters);

        if ($filters['status']) {
            $q->where('purchase_orders.status', $filters['status']);
        }
        if ($filters['supplier_id']) {
            $q->where('purchase_orders.supplier_id', $filters['supplier_id']);
        }

        $rows = $q->orderByDesc('purchase_orders.created_at')->get()->map(fn ($po) => [
            $po->reference,
            $po->title,
            $po->supplier?->name ?? '—',
            number_format((float) $po->total, 2),
            ucfirst(str_replace('_', ' ', $po->status)),
            $po->order_date?->format('d M Y') ?? $po->created_at->format('d M Y'),
        ])->all();

        return [
            'title'   => 'Purchase Orders',
            'columns' => ['Reference', 'Title', 'Supplier', 'Total', 'Status', 'Ordered'],
            'rows'    => $rows,
        ];
    }

    private function invoicesReport(array $filters, int $orgId): array
    {
        $q = SupplierInvoice::with('supplier')->where('supplier_invoices.organization_id', $orgId);

        $this->applyDateRange($q, 'supplier_invoices.invoice_date', $filters);

        if ($filters['status']) {
            $q->where('supplier_invoices.status', $filters['status']);
        }
        if ($filters['supplier_id']) {
            $q->where('supplier_invoices.supplier_id', $filters['supplier_id']);
        }

        $rows = $q->orderByDesc('supplier_invoices.created_at')->get()->map(fn ($i) => [
            $i->number,
            $i->supplier?->name ?? '—',
            number_format((float) $i->subtotal, 2),
            number_format((float) $i->tax_amount, 2),
            number_format((float) $i->total, 2),
            ucfirst(str_replace('_', ' ', $i->status)),
            $i->match_status ? ucfirst(str_replace('_', ' ', $i->match_status)) : '—',
            $i->invoice_date?->format('d M Y') ?? '—',
        ])->all();

        return [
            'title'   => 'Invoices',
            'columns' => ['Number', 'Supplier', 'Subtotal', 'Tax', 'Total', 'Status', 'Match', 'Invoice Date'],
            'rows'    => $rows,
        ];
    }

    private function paymentsReport(array $filters, int $orgId): array
    {
        $q = Payment::with('invoice')->where('payments.organization_id', $orgId);

        $this->applyDateRange($q, 'payments.paid_at', $filters);

        if ($filters['status']) {
            $q->where('payments.status', $filters['status']);
        }

        $rows = $q->orderByDesc('payments.paid_at')->get()->map(fn ($p) => [
            $p->reference,
            $p->invoice?->number ?? '—',
            number_format((float) $p->amount, 2),
            ucfirst($p->method),
            ucfirst(str_replace('_', ' ', $p->status)),
            $p->paid_at?->format('d M Y') ?? '—',
        ])->all();

        return [
            'title'   => 'Payments',
            'columns' => ['Reference', 'Invoice', 'Amount', 'Method', 'Status', 'Paid'],
            'rows'    => $rows,
        ];
    }

    private function budgetsReport(array $filters, int $orgId): array
    {
        $q = Budget::with('department')->where('budgets.organization_id', $orgId);

        $this->applyDateRange($q, 'budgets.created_at', $filters);

        if ($filters['status']) {
            $q->where('budgets.status', $filters['status']);
        }
        if ($filters['department_id']) {
            $q->where('budgets.department_id', $filters['department_id']);
        }
        if ($filters['fiscal_year']) {
            $q->where('budgets.fiscal_year', $filters['fiscal_year']);
        }

        $rows = $q->orderByDesc('budgets.created_at')->get()->map(fn ($b) => [
            $b->name,
            $b->fiscal_year,
            $b->department?->name ?? '—',
            number_format((float) $b->allocated_amount, 2),
            number_format((float) $b->committed_amount, 2),
            number_format((float) $b->spent_amount, 2),
            ucfirst(str_replace('_', ' ', $b->status)),
        ])->all();

        return [
            'title'   => 'Budgets',
            'columns' => ['Name', 'Fiscal Year', 'Department', 'Allocated', 'Committed', 'Spent', 'Status'],
            'rows'    => $rows,
        ];
    }

    private function suppliersReport(array $filters, int $orgId): array
    {
        $q = Supplier::with('category')->where('suppliers.organization_id', $orgId);

        $this->applyDateRange($q, 'suppliers.created_at', $filters);

        if ($filters['status']) {
            $q->where('suppliers.status', $filters['status']);
        }
        if ($filters['category_id']) {
            $q->where('suppliers.category_id', $filters['category_id']);
        }

        $rows = $q->orderByDesc('suppliers.created_at')->get()->map(fn ($s) => [
            $s->name,
            $s->reg_number ?? '—',
            $s->category?->name ?? '—',
            $s->country ?? '—',
            $s->rating ?? '—',
            ucfirst(str_replace('_', ' ', $s->status)),
            $s->created_at->format('d M Y'),
        ])->all();

        return [
            'title'   => 'Suppliers',
            'columns' => ['Name', 'Reg Number', 'Category', 'Country', 'Rating', 'Status', 'Registered'],
            'rows'    => $rows,
        ];
    }

    private function receiptsReport(array $filters, int $orgId): array
    {
        $q = GoodsReceipt::with(['purchaseOrder', 'receiver'])->where('goods_receipts.organization_id', $orgId);

        $this->applyDateRange($q, 'goods_receipts.received_at', $filters);

        if ($filters['status']) {
            $q->whereHas('purchaseOrder', fn ($po) => $po->where('status', $filters['status']));
        }

        $rows = $q->orderByDesc('goods_receipts.received_at')->get()->map(fn ($r) => [
            $r->purchaseOrder?->reference ?? '—',
            $r->receiver?->name ?? '—',
            $r->received_at?->format('d M Y H:i') ?? '—',
            $r->note ?? '—',
        ])->all();

        return [
            'title'   => 'Goods Receipts',
            'columns' => ['PO Reference', 'Received By', 'Received At', 'Note'],
            'rows'    => $rows,
        ];
    }

    private function statusOptions(string $type): array
    {
        return match ($type) {
            'requests'       => ['draft', 'submitted', 'approved', 'rejected'],
            'tenders'        => ['draft', 'published', 'closed', 'awarded', 'cancelled'],
            'contracts'      => ['draft', 'active', 'completed', 'terminated'],
            'purchase-orders'=> ['draft', 'issued', 'approved', 'received', 'cancelled'],
            'invoices'       => ['submitted', 'verified', 'approved', 'paid', 'rejected'],
            'payments'       => ['pending', 'completed', 'failed'],
            'budgets'        => ['active', 'committed', 'closed'],
            'suppliers'      => ['pending', 'approved', 'suspended', 'rejected'],
            'receipts'       => ['draft', 'issued', 'approved', 'received', 'cancelled'],
            default          => [],
        };
    }
}
