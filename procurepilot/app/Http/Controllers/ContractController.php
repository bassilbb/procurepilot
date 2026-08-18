<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Contract;
use App\Models\ContractDocument;
use App\Models\ContractMilestone;
use App\Models\Supplier;
use App\Models\Tender;
use App\Services\ReferenceService;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $org = currentOrganization();

        $query = Contract::where('organization_id', $org->id)
            ->with('supplier')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $contracts = $query->paginate(10)->withQueryString();
        $statuses = ['draft', 'active', 'completed', 'terminated', 'expired'];

        return view('contracts.index', compact('contracts', 'statuses'));
    }

    public function create()
    {
        $suppliers = Supplier::where('organization_id', currentOrganization()->id)->where('status', 'approved')->orderBy('name')->get();
        $tenders = Tender::where('organization_id', currentOrganization()->id)->where('status', 'awarded')->orderBy('title')->get();

        return view('contracts.create', compact('suppliers', 'tenders'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['organization_id'] = currentOrganization()->id;
        $data['reference'] = ReferenceService::contract(currentOrganization());
        $data['created_by'] = auth()->id();

        $contract = Contract::create($data);

        AuditLog::record('contract_created', $contract, [], $contract->toArray());

        return redirect()->route('contracts.show', $contract)->with('success', 'Contract created.');
    }

    public function show(Contract $contract)
    {
        abort_unless($contract->organization_id === currentOrganization()->id, 403);

        $contract->load(['supplier', 'tender', 'award', 'milestones', 'documents', 'purchaseOrders', 'creator']);

        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        abort_unless($contract->organization_id === currentOrganization()->id, 403);

        $suppliers = Supplier::where('organization_id', currentOrganization()->id)->where('status', 'approved')->orderBy('name')->get();

        return view('contracts.edit', compact('contract', 'suppliers'));
    }

    public function update(Request $request, Contract $contract)
    {
        abort_unless($contract->organization_id === currentOrganization()->id, 403);

        $before = $contract->toArray();
        $contract->update($this->validateData($request));

        AuditLog::record('contract_updated', $contract, $before, $contract->toArray());

        return back()->with('success', 'Contract updated.');
    }

    public function activate(Contract $contract)
    {
        abort_unless($contract->organization_id === currentOrganization()->id, 403);

        $before = $contract->toArray();
        $contract->update(['status' => 'active', 'signed_at' => $contract->signed_at ?? now()]);

        AuditLog::record('contract_activated', $contract, $before, $contract->toArray());

        return back()->with('success', 'Contract activated.');
    }

    public function complete(Contract $contract)
    {
        abort_unless($contract->organization_id === currentOrganization()->id, 403);

        $before = $contract->toArray();
        $contract->update(['status' => 'completed']);

        AuditLog::record('contract_completed', $contract, $before, $contract->toArray());

        return back()->with('success', 'Contract marked as completed.');
    }

    public function terminate(Contract $contract)
    {
        abort_unless($contract->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);

        $before = $contract->toArray();
        $contract->update(['status' => 'terminated']);

        AuditLog::record('contract_terminated', $contract, $before, $contract->toArray());

        return back()->with('success', 'Contract terminated.');
    }

    public function storeMilestone(Request $request, Contract $contract)
    {
        abort_unless($contract->organization_id === currentOrganization()->id, 403);

        $data = $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'due_date' => ['required', 'date'],
            'amount'   => ['required', 'numeric', 'min:0'],
        ]);

        $data['contract_id'] = $contract->id;
        ContractMilestone::create($data);

        return back()->with('success', 'Milestone added.');
    }

    public function completeMilestone(ContractMilestone $milestone)
    {
        abort_unless($milestone->contract->organization_id === currentOrganization()->id, 403);

        $milestone->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Milestone completed.');
    }

    public function destroyMilestone(ContractMilestone $milestone)
    {
        abort_unless($milestone->contract->organization_id === currentOrganization()->id, 403);

        $milestone->delete();

        return back()->with('success', 'Milestone removed.');
    }

    public function uploadDocument(Request $request, Contract $contract)
    {
        abort_unless($contract->organization_id === currentOrganization()->id, 403);

        $request->validate([
            'documents' => ['required', 'array'],
            'documents.*' => ['file', 'max:10240'],
        ]);

        foreach ($request->file('documents') as $file) {
            $contract->documents()->create([
                'name' => $file->getClientOriginalName(),
                'path' => $file->store('contract-documents', 'public'),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        return back()->with('success', 'Documents uploaded.');
    }

    public function downloadDocument(ContractDocument $document)
    {
        abort_unless($document->contract->organization_id === currentOrganization()->id, 403);

        return response()->download(storage_path('app/public/' . $document->path));
    }

    public function destroyDocument(ContractDocument $document)
    {
        abort_unless($document->contract->organization_id === currentOrganization()->id, 403);

        $document->delete();

        return back()->with('success', 'Document removed.');
    }

    public function destroy(Contract $contract)
    {
        abort_unless($contract->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);

        AuditLog::record('contract_removed', $contract, $contract->toArray(), []);
        $contract->delete();

        return redirect()->route('contracts.index')->with('success', 'Contract removed.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'supplier_id'   => ['required', 'exists:suppliers,id'],
            'tender_id'     => ['nullable', 'exists:tenders,id'],
            'value'         => ['required', 'numeric', 'min:0'],
            'currency'      => ['nullable', 'string', 'max:10'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['required', 'date', 'after_or_equal:start_date'],
            'payment_terms' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
