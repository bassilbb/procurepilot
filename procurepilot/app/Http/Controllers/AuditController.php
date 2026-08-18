<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Bid;
use App\Models\Contract;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Tender;

class AuditController extends Controller
{
    public function index()
    {
        $logs = AuditLog::where('organization_id', currentOrganization()->id)
            ->with('user')
            ->latest()
            ->paginate(30);

        return view('audit.index', compact('logs'));
    }

    public function compliance()
    {
        $org = currentOrganization();

        $stats = [
            'suppliers'   => Supplier::where('organization_id', $org->id)->count(),
            'approved_suppliers' => Supplier::where('organization_id', $org->id)->where('status', 'approved')->count(),
            'tenders'     => Tender::where('organization_id', $org->id)->count(),
            'open_tenders'=> Tender::where('organization_id', $org->id)->where('status', 'published')->count(),
            'bids'        => Bid::where('organization_id', $org->id)->count(),
            'contracts'   => Contract::where('organization_id', $org->id)->count(),
            'active_contracts' => Contract::where('organization_id', $org->id)->where('status', 'active')->count(),
            'pos'         => PurchaseOrder::where('organization_id', $org->id)->count(),
        ];

        return view('audit.compliance', compact('stats'));
    }
}
