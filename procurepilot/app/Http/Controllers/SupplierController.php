<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\SupplierDocument;
use App\Models\SupplierDocumentRequirement;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $org = currentOrganization();

        $query = Supplier::where('organization_id', $org->id)
            ->with('category')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->q}%")
                    ->orWhere('email', 'like', "%{$request->q}%")
                    ->orWhere('reg_number', 'like', "%{$request->q}%");
            });
        }

        $suppliers = $query->paginate(12)->withQueryString();
        $statuses = ['pending', 'approved', 'suspended', 'blacklisted'];

        return view('suppliers.index', compact('suppliers', 'statuses'));
    }

    public function create()
    {
        $categories = Category::where('organization_id', currentOrganization()->id)->orderBy('name')->get();
        $requirements = SupplierDocumentRequirement::where('organization_id', currentOrganization()->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('suppliers.create', compact('categories', 'requirements'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['organization_id'] = currentOrganization()->id;
        $data['status'] = 'pending';

        $supplier = Supplier::create($data);

        $this->handleDocuments($supplier, $request);

        AuditLog::record('supplier_registered', $supplier, [], $supplier->toArray());

        return redirect()->route('suppliers.show', $supplier)->with('success', 'Supplier registered and queued for vetting.');
    }

    public function show(Supplier $supplier)
    {
        abort_unless($supplier->organization_id === currentOrganization()->id, 403);

        $supplier->load(['category', 'documents.requirement', 'contracts', 'bids.tender']);

        $requirements = SupplierDocumentRequirement::where('organization_id', currentOrganization()->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('suppliers.show', compact('supplier', 'requirements'));
    }

    public function edit(Supplier $supplier)
    {
        abort_unless($supplier->organization_id === currentOrganization()->id, 403);

        $categories = Category::where('organization_id', currentOrganization()->id)->orderBy('name')->get();
        $requirements = SupplierDocumentRequirement::where('organization_id', currentOrganization()->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        $supplier->load('documents.requirement');

        return view('suppliers.edit', compact('supplier', 'categories', 'requirements'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        abort_unless($supplier->organization_id === currentOrganization()->id, 403);

        $before = $supplier->toArray();
        $supplier->update($this->validateData($request));
        $this->handleDocuments($supplier, $request);

        AuditLog::record('supplier_updated', $supplier, $before, $supplier->toArray());

        return redirect()->route('suppliers.show', $supplier)->with('success', 'Supplier updated.');
    }

    public function approve(Supplier $supplier)
    {
        abort_unless($supplier->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);

        $before = $supplier->toArray();
        $supplier->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        AuditLog::record('supplier_approved', $supplier, $before, $supplier->toArray());

        return back()->with('success', 'Supplier approved and vetted.');
    }

    public function setStatus(Request $request, Supplier $supplier)
    {
        abort_unless($supplier->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);

        $request->validate(['status' => ['required', 'in:approved,suspended,blacklisted,pending']]);

        $before = $supplier->toArray();
        $supplier->update(['status' => $request->status, 'notes' => $request->notes ?? $supplier->notes]);

        AuditLog::record('supplier_status_changed', $supplier, $before, $supplier->toArray());

        return back()->with('success', 'Supplier status updated to ' . ucfirst($request->status) . '.');
    }

    public function destroy(Supplier $supplier)
    {
        abort_unless($supplier->organization_id === currentOrganization()->id, 403);

        AuditLog::record('supplier_removed', $supplier, $supplier->toArray(), []);
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier removed.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'reg_number'           => ['nullable', 'string', 'max:255'],
            'email'                => ['nullable', 'email', 'max:255'],
            'phone'                => ['nullable', 'string', 'max:60'],
            'address'              => ['nullable', 'string'],
            'country'              => ['nullable', 'string', 'max:120'],
            'category_id'          => ['nullable', 'exists:categories,id'],
            'tax_id'               => ['nullable', 'string', 'max:255'],
            'bank_account_name'    => ['nullable', 'string', 'max:255'],
            'bank_account_number'  => ['nullable', 'string', 'max:120'],
            'bank_name'            => ['nullable', 'string', 'max:255'],
            'certifications'       => ['nullable', 'string'],
            'notes'                => ['nullable', 'string'],
        ]);
    }

    protected function handleDocuments(Supplier $supplier, Request $request): void
    {
        // Files keyed by requirement_id: documents[12][] = [files]
        $filesByRequirement = $request->file('documents');
        if (is_array($filesByRequirement)) {
            foreach ($filesByRequirement as $requirementId => $files) {
                if (! is_numeric($requirementId)) {
                    continue;
                }

                foreach ((array) $files as $file) {
                    if (! $file instanceof \Illuminate\Http\UploadedFile) {
                        continue;
                    }

                    $path = $file->store('supplier-documents', 'public');

                    $supplier->documents()->create([
                        'requirement_id' => (int) $requirementId,
                        'name'           => $file->getClientOriginalName(),
                        'type'           => $file->getClientMimeType(),
                        'path'           => $path,
                        'size'           => $file->getSize(),
                    ]);
                }
            }
        }

        // Generic supporting files
        $otherFiles = $request->file('other_documents');
        if (is_array($otherFiles)) {
            foreach ($otherFiles as $file) {
                if (! $file instanceof \Illuminate\Http\UploadedFile) {
                    continue;
                }

                $path = $file->store('supplier-documents', 'public');

                $supplier->documents()->create([
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientMimeType(),
                    'path' => $path,
                    'size' => $file->getSize(),
                ]);
            }
        }
    }

    public function downloadDocument(SupplierDocument $document)
    {
        abort_unless($document->supplier->organization_id === currentOrganization()->id, 403);

        return response()->download(storage_path('app/public/' . $document->path));
    }

    public function destroyDocument(SupplierDocument $document)
    {
        abort_unless($document->supplier->organization_id === currentOrganization()->id, 403);

        $document->delete();

        return back()->with('success', 'Document removed.');
    }
}
