<?php

namespace App\Http\Controllers;

use App\Models\ApprovalLevel;
use App\Models\ApprovalRecord;
use App\Models\AuditLog;
use App\Models\Award;
use App\Models\Bid;
use App\Models\Budget;
use App\Models\Contract;
use App\Models\GoodsReceipt;
use App\Models\Invoice;
use App\Models\ProcurementPlan;
use App\Models\ProcurementRequest;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierDocumentRequirement;
use App\Models\SupplierInvoice;
use App\Models\Tender;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $org = currentOrganization();

        return match ($user->role) {
            'approver'    => $this->approverDashboard($org),
            'auditor'     => $this->auditorDashboard($org),
            'procurement' => $this->procurementDashboard($org),
            'staff'       => $this->staffDashboard($org),
            default       => $this->adminDashboard($org),
        };
    }

    protected function adminDashboard($org)
    {
        $counts = [
            'suppliers'   => Supplier::where('organization_id', $org->id)->count(),
            'tenders'     => Tender::where('organization_id', $org->id)->count(),
            'bids'        => Bid::where('organization_id', $org->id)->count(),
            'contracts'   => Contract::where('organization_id', $org->id)->count(),
            'purchaseOrders' => PurchaseOrder::where('organization_id', $org->id)->count(),
            'plans'       => ProcurementPlan::where('organization_id', $org->id)->count(),
        ];

        $openTenders = Tender::where('organization_id', $org->id)
            ->where('status', 'published')
            ->where('closing_at', '>', Carbon::now())
            ->count();

        $pendingApprovals = Tender::where('organization_id', $org->id)->where('status', 'closed')->count()
            + Award::where('organization_id', $org->id)->where('status', 'recommended')->count()
            + ProcurementPlan::where('organization_id', $org->id)->where('status', 'submitted')->count()
            + Supplier::where('organization_id', $org->id)->where('status', 'pending')->count();

        $contractValue = (float) Contract::where('organization_id', $org->id)
            ->whereIn('status', ['active', 'draft'])
            ->sum('value');

        $spendByStatus = PurchaseOrder::where('organization_id', $org->id)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('status, sum(total) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthlySpend = $this->monthlySpend($org);

        $categoryDistribution = Tender::where('organization_id', $org->id)
            ->with('category')
            ->get()
            ->groupBy(fn ($t) => $t->category?->name ?? 'Uncategorised')
            ->map->count();

        $recent = collect()
            ->merge(Tender::where('organization_id', $org->id)->with('category')->latest()->take(5)->get())
            ->merge(Contract::where('organization_id', $org->id)->with('supplier')->latest()->take(5)->get())
            ->sortByDesc('created_at')
            ->take(6);

        $expiringContracts = Contract::where('organization_id', $org->id)
            ->where('status', 'active')
            ->where('end_date', '>=', Carbon::now())
            ->where('end_date', '<=', Carbon::now()->addDays(30))
            ->with('supplier')
            ->latest()
            ->take(5)
            ->get();

        $recentInvoices = Invoice::where('organization_id', $org->id)->latest()->take(5)->get();

        $pendingRequestApprovals = $this->pendingRequestApprovals($org);

        $requirements = SupplierDocumentRequirement::where('organization_id', $org->id)
            ->withCount('documents')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('dashboard', compact(
            'counts', 'openTenders', 'pendingApprovals', 'contractValue',
            'spendByStatus', 'monthlySpend', 'categoryDistribution', 'recent',
            'expiringContracts', 'recentInvoices', 'pendingRequestApprovals',
            'requirements'
        ));
    }

    protected function procurementDashboard($org)
    {
        $counts = [
            'suppliers'   => Supplier::where('organization_id', $org->id)->count(),
            'tenders'     => Tender::where('organization_id', $org->id)->count(),
            'contracts'   => Contract::where('organization_id', $org->id)->count(),
            'purchaseOrders' => PurchaseOrder::where('organization_id', $org->id)->count(),
            'plans'       => ProcurementPlan::where('organization_id', $org->id)->count(),
            'budgets'     => Budget::where('organization_id', $org->id)->count(),
        ];

        $openTenders = Tender::where('organization_id', $org->id)
            ->where('status', 'published')
            ->where('closing_at', '>', Carbon::now())
            ->count();

        $pendingSuppliers = Supplier::where('organization_id', $org->id)->where('status', 'pending')->count();
        $pendingRequests = ProcurementRequest::where('organization_id', $org->id)->where('status', 'submitted')->count();
        $draftRequests = ProcurementRequest::where('organization_id', $org->id)->where('status', 'draft')->count();

        $contractValue = (float) Contract::where('organization_id', $org->id)
            ->whereIn('status', ['active', 'draft'])
            ->sum('value');

        $monthlySpend = $this->monthlySpend($org);

        $recentRequests = ProcurementRequest::where('organization_id', $org->id)
            ->with(['department', 'category'])
            ->latest()
            ->take(6)
            ->get();

        $expiringContracts = Contract::where('organization_id', $org->id)
            ->where('status', 'active')
            ->where('end_date', '>=', Carbon::now())
            ->where('end_date', '<=', Carbon::now()->addDays(60))
            ->with('supplier')
            ->latest()
            ->take(5)
            ->get();

        $recentTenders = Tender::where('organization_id', $org->id)->with('category')->latest()->take(5)->get();
        $budgets = Budget::where('organization_id', $org->id)->where('status', 'active')->get();

        return view('dashboard.procurement', compact(
            'counts', 'openTenders', 'pendingSuppliers', 'pendingRequests', 'draftRequests',
            'contractValue', 'monthlySpend', 'recentRequests', 'expiringContracts',
            'recentTenders', 'budgets'
        ));
    }

    protected function approverDashboard($org)
    {
        $pendingRequestApprovals = $this->pendingRequestApprovals($org);

        $recommendedAwards = Award::where('organization_id', $org->id)
            ->where('status', 'recommended')
            ->with(['tender', 'supplier'])
            ->latest()
            ->get();

        $closedTenders = Tender::where('organization_id', $org->id)
            ->where('status', 'closed')
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        $submittedPlans = ProcurementPlan::where('organization_id', $org->id)
            ->where('status', 'submitted')
            ->latest()
            ->take(5)
            ->get();

        $pendingSuppliers = Supplier::where('organization_id', $org->id)
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $recentDecisions = ApprovalRecord::where('organization_id', $org->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->with(['approvable', 'approver'])
            ->latest()
            ->take(6)
            ->get();

        $counts = [
            'requests'      => ProcurementRequest::where('organization_id', $org->id)->where('status', 'submitted')->count(),
            'awards'        => Award::where('organization_id', $org->id)->where('status', 'recommended')->count(),
            'closedTenders' => Tender::where('organization_id', $org->id)->where('status', 'closed')->count(),
            'suppliers'     => Supplier::where('organization_id', $org->id)->where('status', 'pending')->count(),
            'contracts'     => Contract::where('organization_id', $org->id)->whereIn('status', ['active', 'draft'])->count(),
        ];

        return view('dashboard.approver', compact(
            'pendingRequestApprovals', 'recommendedAwards', 'closedTenders',
            'submittedPlans', 'pendingSuppliers', 'recentDecisions', 'counts'
        ));
    }

    protected function auditorDashboard($org)
    {
        $contractValue = (float) Contract::where('organization_id', $org->id)
            ->whereIn('status', ['active', 'draft'])
            ->sum('value');

        $spendByStatus = PurchaseOrder::where('organization_id', $org->id)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('status, sum(total) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthlySpend = $this->monthlySpend($org);

        $counts = [
            'contracts'   => Contract::where('organization_id', $org->id)->count(),
            'invoices'    => SupplierInvoice::where('organization_id', $org->id)->count(),
            'tenders'     => Tender::where('organization_id', $org->id)->count(),
            'suppliers'   => Supplier::where('organization_id', $org->id)->count(),
            'requests'    => ProcurementRequest::where('organization_id', $org->id)->count(),
            'auditLogs'   => AuditLog::where('organization_id', $org->id)->count(),
        ];

        $recentAuditLogs = AuditLog::where('organization_id', $org->id)
            ->with('user')
            ->latest()
            ->take(6)
            ->get();

        $matchedInvoices = SupplierInvoice::where('organization_id', $org->id)
            ->where('match_status', 'full')
            ->count();
        $unmatchedInvoices = SupplierInvoice::where('organization_id', $org->id)
            ->where('match_status', '!=', 'full')
            ->count();

        $contracts = Contract::where('organization_id', $org->id)
            ->where('status', 'active')
            ->with('supplier')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.auditor', compact(
            'contractValue', 'spendByStatus', 'monthlySpend', 'counts',
            'recentAuditLogs', 'matchedInvoices', 'unmatchedInvoices', 'contracts'
        ));
    }

    protected function staffDashboard($org)
    {
        $user = auth()->user();

        $counts = [
            'requests' => ProcurementRequest::where('organization_id', $org->id)
                ->where('requester_id', $user->id)->count(),
            'submitted' => ProcurementRequest::where('organization_id', $org->id)
                ->where('requester_id', $user->id)->where('status', 'submitted')->count(),
            'approved' => ProcurementRequest::where('organization_id', $org->id)
                ->where('requester_id', $user->id)->where('status', 'approved')->count(),
            'drafts' => ProcurementRequest::where('organization_id', $org->id)
                ->where('requester_id', $user->id)->where('status', 'draft')->count(),
        ];

        $myRequests = ProcurementRequest::where('organization_id', $org->id)
            ->where('requester_id', $user->id)
            ->latest()
            ->take(6)
            ->get();

        $recentTenders = Tender::where('organization_id', $org->id)
            ->where('status', 'published')
            ->where('closing_at', '>', Carbon::now())
            ->with('category')
            ->latest()
            ->take(4)
            ->get();

        return view('dashboard.staff', compact('counts', 'myRequests', 'recentTenders'));
    }

    protected function monthlySpend($org)
    {
        return PurchaseOrder::where('organization_id', $org->id)
            ->where('status', '!=', 'cancelled')
            ->whereYear('order_date', Carbon::now()->year)
            ->selectRaw("strftime('%m', order_date) as month, sum(total) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');
    }

    protected function pendingRequestApprovals($org): array
    {
        // Approval records pending at a level this user's role can act on
        $user = auth()->user();

        $levels = ApprovalLevel::where('organization_id', $org->id)
            ->where('is_active', true)
            ->get()
            ->filter(fn ($level) => in_array($user->role, $level->permittedRoles()))
            ->pluck('id');

        $pending = ApprovalRecord::where('organization_id', $org->id)
            ->where('status', 'pending')
            ->whereIn('approval_level_id', $levels)
            ->with('approvable')
            ->get()
            ->map(fn ($record) => $record->approvable)
            ->filter()
            ->unique('id')
            ->values();

        // Also fall back to requests awaiting any pending approval step (visible to admins)
        if ($user->isFullAccess()) {
            $allPending = ApprovalRecord::where('organization_id', $org->id)
                ->where('status', 'pending')
                ->with('approvable')
                ->get()
                ->map(fn ($record) => $record->approvable)
                ->filter()
                ->unique('id')
                ->values();

            return $allPending->merge($pending)->unique('id')->values()->all();
        }

        return $pending->all();
    }
}
