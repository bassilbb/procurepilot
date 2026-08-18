<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Award;
use App\Models\Bid;
use App\Models\Category;
use App\Models\EvaluationCriterion;
use App\Models\Supplier;
use App\Models\Tender;
use App\Models\TenderItem;
use App\Services\ReferenceService;
use Illuminate\Http\Request;

class TenderController extends Controller
{
    public function index(Request $request)
    {
        $org = currentOrganization();

        $query = Tender::where('organization_id', $org->id)
            ->with('category')
            ->withCount('bids')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $query->where('title', 'like', "%{$request->q}%");
        }

        $tenders = $query->paginate(10)->withQueryString();
        $statuses = ['draft', 'published', 'closed', 'under_evaluation', 'awarded', 'cancelled'];

        return view('tenders.index', compact('tenders', 'statuses'));
    }

    public function create()
    {
        $categories = Category::where('organization_id', currentOrganization()->id)->orderBy('name')->get();

        return view('tenders.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['organization_id'] = currentOrganization()->id;
        $data['created_by'] = auth()->id();
        $data['reference'] = ReferenceService::tender(currentOrganization());

        $tender = Tender::create($data);

        $this->storeItems($tender, $request);

        AuditLog::record('tender_created', $tender, [], $tender->toArray());

        return redirect()->route('tenders.show', $tender)->with('success', 'Tender created. Configure items, criteria and publish.');
    }

    public function show(Tender $tender)
    {
        abort_unless($tender->organization_id === currentOrganization()->id, 403);

        $tender->load(['category', 'items', 'criteria', 'suppliers', 'bids.supplier', 'awards.supplier', 'creator', 'approver']);

        $approvedSuppliers = Supplier::where('organization_id', currentOrganization()->id)
            ->where('status', 'approved')
            ->orderBy('name')
            ->get();

        return view('tenders.show', compact('tender', 'approvedSuppliers'));
    }

    public function edit(Tender $tender)
    {
        abort_unless($tender->organization_id === currentOrganization()->id, 403);

        $categories = Category::where('organization_id', currentOrganization()->id)->orderBy('name')->get();
        $tender->load(['items', 'criteria']);

        return view('tenders.edit', compact('tender', 'categories'));
    }

    public function update(Request $request, Tender $tender)
    {
        abort_unless($tender->organization_id === currentOrganization()->id, 403);

        $before = $tender->toArray();
        $tender->update($this->validateData($request));

        AuditLog::record('tender_updated', $tender, $before, $tender->toArray());

        return back()->with('success', 'Tender updated.');
    }

    public function storeItem(Request $request, Tender $tender)
    {
        abort_unless($tender->organization_id === currentOrganization()->id, 403);

        $data = $request->validate([
            'description'          => ['required', 'string'],
            'quantity'             => ['required', 'numeric', 'min:0.01'],
            'unit'                 => ['nullable', 'string', 'max:60'],
            'estimated_unit_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $data['tender_id'] = $tender->id;
        TenderItem::create($data);

        return back()->with('success', 'Line item added.');
    }

    public function destroyItem(TenderItem $item)
    {
        abort_unless($item->tender->organization_id === currentOrganization()->id, 403);

        $item->delete();

        return back()->with('success', 'Line item removed.');
    }

    public function storeCriterion(Request $request, Tender $tender)
    {
        abort_unless($tender->organization_id === currentOrganization()->id, 403);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'weight'      => ['required', 'integer', 'min:0', 'max:100'],
            'max_score'   => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $data['tender_id'] = $tender->id;
        EvaluationCriterion::create($data);

        return back()->with('success', 'Evaluation criterion added.');
    }

    public function destroyCriterion(EvaluationCriterion $criterion)
    {
        abort_unless($criterion->tender->organization_id === currentOrganization()->id, 403);

        $criterion->delete();

        return back()->with('success', 'Evaluation criterion removed.');
    }

    public function inviteSupplier(Request $request, Tender $tender)
    {
        abort_unless($tender->organization_id === currentOrganization()->id, 403);

        $request->validate(['supplier_ids' => ['required', 'array']]);

        $tender->suppliers()->syncWithoutDetaching($request->supplier_ids);

        return back()->with('success', 'Suppliers invited to tender.');
    }

    public function removeSupplier(Tender $tender, Supplier $supplier)
    {
        abort_unless($tender->organization_id === currentOrganization()->id, 403);

        $tender->suppliers()->detach($supplier->id);

        return back()->with('success', 'Supplier removed from tender.');
    }

    public function publish(Tender $tender)
    {
        abort_unless($tender->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isProcurement(), 403);

        $before = $tender->toArray();
        $tender->update([
            'status'       => 'published',
            'published_at' => now(),
        ]);

        AuditLog::record('tender_published', $tender, $before, $tender->toArray());

        return back()->with('success', 'Tender published. Invitations have been dispatched.');
    }

    public function close(Tender $tender)
    {
        abort_unless($tender->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);

        $before = $tender->toArray();
        $tender->update(['status' => 'closed']);

        AuditLog::record('tender_closed', $tender, $before, $tender->toArray());

        return back()->with('success', 'Tender closed for bid submission.');
    }

    public function beginEvaluation(Tender $tender)
    {
        abort_unless($tender->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);

        $before = $tender->toArray();
        $tender->update(['status' => 'under_evaluation']);

        AuditLog::record('tender_evaluation_started', $tender, $before, $tender->toArray());

        return redirect()->route('tenders.evaluate', $tender)->with('success', 'Evaluation session started.');
    }

    public function evaluate(Tender $tender)
    {
        abort_unless($tender->organization_id === currentOrganization()->id, 403);

        $tender->load(['criteria', 'bids.supplier', 'bids.scores', 'bids.items']);

        return view('tenders.evaluate', compact('tender'));
    }

    public function cancel(Tender $tender)
    {
        abort_unless($tender->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isProcurement(), 403);

        $before = $tender->toArray();
        $tender->update(['status' => 'cancelled']);

        AuditLog::record('tender_cancelled', $tender, $before, $tender->toArray());

        return back()->with('success', 'Tender cancelled.');
    }

    public function destroy(Tender $tender)
    {
        abort_unless($tender->organization_id === currentOrganization()->id, 403);

        if (in_array($tender->status, ['published', 'closed', 'under_evaluation', 'awarded'])) {
            return back()->withErrors(['Tenders that have progressed cannot be deleted. Cancel them instead.']);
        }

        AuditLog::record('tender_removed', $tender, $tender->toArray(), []);
        $tender->delete();

        return redirect()->route('tenders.index')->with('success', 'Tender removed.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'title'              => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string'],
            'category_id'        => ['nullable', 'exists:categories,id'],
            'type'               => ['required', 'in:open,restricted'],
            'method'             => ['required', 'in:open_competitive,restricted,direct'],
            'budget'             => ['nullable', 'numeric', 'min:0'],
            'currency'           => ['nullable', 'string', 'max:10'],
            'closing_at'         => ['nullable', 'date'],
            'opening_at'         => ['nullable', 'date'],
            'evaluation_method'  => ['required', 'in:lowest_price,weighted_score,quality_cost'],
        ]);
    }

    protected function storeItems(Tender $tender, Request $request): void
    {
        if (! $request->has('items')) {
            return;
        }

        foreach ($request->input('items', []) as $item) {
            if (empty($item['description'])) {
                continue;
            }

            $tender->items()->create([
                'description'          => $item['description'],
                'quantity'             => $item['quantity'] ?? 1,
                'unit'                 => $item['unit'] ?? null,
                'estimated_unit_price' => $item['estimated_unit_price'] ?? null,
            ]);
        }
    }
}
