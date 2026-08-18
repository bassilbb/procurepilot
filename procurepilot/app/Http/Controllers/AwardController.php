<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Award;
use App\Models\Contract;
use App\Services\ReferenceService;
use Illuminate\Http\Request;

class AwardController extends Controller
{
    public function index()
    {
        $awards = Award::where('organization_id', currentOrganization()->id)
            ->with(['tender', 'supplier', 'bid'])
            ->latest()
            ->get();

        return view('awards.index', compact('awards'));
    }

    public function show(Award $award)
    {
        abort_unless($award->organization_id === currentOrganization()->id, 403);

        $award->load(['tender', 'supplier', 'bid', 'decisionMaker', 'contract']);

        return view('awards.show', compact('award'));
    }

    public function approve(Award $award)
    {
        abort_unless($award->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);

        $before = $award->toArray();

        $award->update([
            'status'      => 'approved',
            'decided_by'  => auth()->id(),
            'decided_at'  => now(),
        ]);

        $award->bid->update(['status' => 'awarded']);
        $award->tender->update([
            'status'       => 'awarded',
            'award_notice' => 'Awarded to ' . $award->supplier->name . ' for ' . number_format($award->award_amount, 2) . ' ' . $award->currency,
            'approved_by'  => auth()->id(),
            'approved_at'  => now(),
        ]);

        AuditLog::record('award_approved', $award, $before, $award->toArray());

        return back()->with('success', 'Award approved by the Tenders Board.');
    }

    public function decline(Request $request, Award $award)
    {
        abort_unless($award->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);

        $before = $award->toArray();

        $award->update([
            'status'        => 'declined',
            'justification' => $award->justification . "\n\nDecline reason: " . ($request->reason ?? ''),
            'decided_by'    => auth()->id(),
            'decided_at'    => now(),
        ]);

        $award->bid->update(['status' => 'evaluated']);
        $award->tender->update(['status' => 'under_evaluation']);

        AuditLog::record('award_declined', $award, $before, $award->toArray());

        return back()->with('success', 'Award declined.');
    }

    public function createContract(Award $award)
    {
        abort_unless($award->organization_id === currentOrganization()->id, 403);
        abort_unless($award->status === 'approved', 403, 'Only approved awards can be converted to contracts.');

        if ($award->contract) {
            return redirect()->route('contracts.show', $award->contract)->with('info', 'A contract already exists for this award.');
        }

        $contract = Contract::create([
            'organization_id' => currentOrganization()->id,
            'reference'       => ReferenceService::contract(currentOrganization()),
            'title'           => 'Contract — ' . $award->tender->title,
            'description'     => $award->tender->description,
            'supplier_id'     => $award->supplier_id,
            'tender_id'       => $award->tender_id,
            'award_id'        => $award->id,
            'value'           => $award->award_amount,
            'currency'        => $award->currency,
            'start_date'      => now()->addDay(),
            'end_date'        => now()->addYear(),
            'payment_terms'   => 'Net 30',
            'status'          => 'draft',
            'created_by'      => auth()->id(),
            'signed_at'       => now(),
        ]);

        AuditLog::record('contract_created_from_award', $contract, [], $contract->toArray());

        return redirect()->route('contracts.show', $contract)->with('success', 'Contract created from award. Complete the details and activate.');
    }
}
