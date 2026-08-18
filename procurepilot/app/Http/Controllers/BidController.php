<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Award;
use App\Models\Bid;
use App\Models\BidItem;
use App\Models\BidScore;
use App\Models\Supplier;
use App\Models\Tender;
use App\Services\ReferenceService;
use Illuminate\Http\Request;

class BidController extends Controller
{
    public function store(Request $request, Tender $tender)
    {
        abort_unless($tender->organization_id === currentOrganization()->id, 403);
        abort_unless($tender->isOpen(), 403, 'This tender is not open for bidding.');

        $request->validate([
            'supplier_id'          => ['required', 'exists:suppliers,id,organization_id,' . currentOrganization()->id],
            'compliance_declaration' => ['nullable', 'string'],
            'items'                => ['required', 'array', 'min:1'],
            'items.*.description'  => ['required', 'string'],
            'items.*.quantity'     => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price'   => ['required', 'numeric', 'min:0'],
        ]);

        $total = collect($request->items)->sum(fn ($i) => $i['quantity'] * $i['unit_price']);

        $bid = Bid::create([
            'organization_id'        => currentOrganization()->id,
            'tender_id'              => $tender->id,
            'supplier_id'            => $request->supplier_id,
            'reference'              => ReferenceService::bid(currentOrganization()),
            'total_amount'           => $total,
            'currency'               => $tender->currency,
            'compliance_declaration' => $request->compliance_declaration,
            'status'                 => 'submitted',
            'submitted_at'           => now(),
        ]);

        foreach ($request->items as $item) {
            $bid->items()->create([
                'description'  => $item['description'],
                'quantity'     => $item['quantity'],
                'unit'         => $item['unit'] ?? null,
                'unit_price'   => $item['unit_price'],
                'total_price'  => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $tender->suppliers()->syncWithoutDetaching($request->supplier_id);

        AuditLog::record('bid_submitted', $bid, [], $bid->toArray());

        return redirect()->route('bids.show', $bid)->with('success', 'Bid submitted successfully.');
    }

    public function show(Bid $bid)
    {
        abort_unless($bid->organization_id === currentOrganization()->id, 403);

        $bid->load(['tender', 'supplier', 'items', 'scores.criterion', 'evaluator', 'award']);

        return view('bids.show', compact('bid'));
    }

    public function score(Request $request, Bid $bid)
    {
        abort_unless($bid->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);

        $request->validate([
            'scores'     => ['required', 'array'],
            'scores.*'   => ['nullable', 'numeric', 'min:0'],
            'comments'   => ['nullable', 'array'],
        ]);

        $totalWeight = (int) $bid->tender->criteria->sum('weight');

        foreach ($bid->tender->criteria as $criterion) {
            $score = (float) ($request->scores[$criterion->id] ?? 0);

            $weighted = $totalWeight > 0
                ? ($score / ($criterion->max_score ?: 100)) * $criterion->weight
                : $score;

            BidScore::updateOrCreate(
                ['bid_id' => $bid->id, 'criterion_id' => $criterion->id],
                [
                    'evaluator_id' => auth()->id(),
                    'score'        => $score,
                    'comment'      => $request->comments[$criterion->id] ?? null,
                ]
            );
        }

        $technicalScore = $totalWeight > 0 ? (float) $bid->scores->sum(fn ($s) => ($s->score / ($s->criterion->max_score ?: 100)) * $s->criterion->weight) : 0;

        $bid->update([
            'technical_score' => $technicalScore,
            'status'          => 'evaluated',
            'evaluated_by'    => auth()->id(),
            'evaluated_at'    => now(),
        ]);

        AuditLog::record('bid_scored', $bid, [], ['technical_score' => $technicalScore]);

        return back()->with('success', 'Bid scores saved.');
    }

    public function recommendAward(Request $request, Bid $bid)
    {
        abort_unless($bid->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);

        $request->validate([
            'award_amount' => ['required', 'numeric', 'min:0'],
            'justification'=> ['nullable', 'string'],
        ]);

        $award = Award::create([
            'organization_id' => currentOrganization()->id,
            'tender_id'       => $bid->tender_id,
            'bid_id'          => $bid->id,
            'supplier_id'     => $bid->supplier_id,
            'award_amount'    => $request->award_amount,
            'currency'        => $bid->tender->currency,
            'justification'   => $request->justification,
            'status'          => 'recommended',
            'decided_by'      => auth()->id(),
            'decided_at'      => now(),
        ]);

        $bid->update(['status' => 'evaluated']);
        $bid->tender->update(['status' => 'under_evaluation']);

        AuditLog::record('award_recommended', $award, [], $award->toArray());

        return redirect()->route('awards.show', $award)->with('success', 'Award recommended to the Tenders Board for approval.');
    }

    public function withdraw(Bid $bid)
    {
        abort_unless($bid->organization_id === currentOrganization()->id, 403);

        $before = $bid->toArray();
        $bid->update(['status' => 'withdrawn']);

        AuditLog::record('bid_withdrawn', $bid, $before, $bid->toArray());

        return back()->with('success', 'Bid withdrawn.');
    }
}
