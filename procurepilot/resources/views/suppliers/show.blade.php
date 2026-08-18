<x-app-layout>
    <x-slot name="header">Supplier Profile</x-slot>

    <x-page-header :title="$supplier->name">
        <x-slot name="actions">
            <span class="text-xs px-3 py-1.5 rounded-full border font-medium {{ statusBadgeClass($supplier->status) }}">{{ $supplier->statusLabel() }}</span>
            <a href="{{ route('suppliers.edit', $supplier) }}" class="px-4 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Edit</a>
            @if ($supplier->status !== 'approved' && auth()->user()->isApprover())
                <form method="POST" action="{{ route('suppliers.approve', $supplier) }}">
                    @csrf
                    <button class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">Approve & Vet</button>
                </form>
            @endif
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Company Information</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-slate-500">Registration Number</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $supplier->reg_number ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Tax ID</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $supplier->tax_id ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Email</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $supplier->email ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Phone</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $supplier->phone ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Category</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $supplier->category?->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Country</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $supplier->country ?? '—' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">Address</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $supplier->address ?? '—' }}</dd></div>
                    @if ($supplier->certifications)
                        <div class="sm:col-span-2"><dt class="text-slate-500">Certifications</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $supplier->certifications }}</dd></div>
                    @endif
                </dl>
            </div>

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Bank Details</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div><dt class="text-slate-500">Account Name</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $supplier->bank_account_name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Bank</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $supplier->bank_name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Account Number</dt><dd class="font-medium text-slate-900 mt-0.5">{{ $supplier->bank_account_number ?? '—' }}</dd></div>
                </dl>
            </div>

            <div class="hd-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-900">Compliance Checklist</h3>
                    @php
                        $requiredCount = $requirements->where('is_required', true)->count();
                        $metCount = $requirements->where('is_required', true)->filter(fn ($r) => $supplier->documents->where('requirement_id', $r->id)->isNotEmpty())->count();
                        $complete = $requiredCount === 0 || $metCount === $requiredCount;
                    @endphp
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $complete ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                        {{ $metCount }}/{{ $requiredCount }} required met
                    </span>
                </div>

                @if ($requirements->isEmpty())
                    <p class="text-sm text-slate-500">No document requirements configured for this organization.</p>
                @else
                    <div class="space-y-2.5">
                        @foreach ($requirements as $requirement)
                            @php $hasDoc = $supplier->documents->where('requirement_id', $requirement->id)->isNotEmpty(); @endphp
                            <div class="flex items-start gap-3 p-3 rounded-lg {{ $requirement->is_required ? 'bg-slate-50' : 'bg-slate-50/50' }}">
                                <span class="mt-0.5 w-5 h-5 rounded-full flex items-center justify-center shrink-0 {{ $hasDoc ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-600' }}">
                                    @if ($hasDoc)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    @endif
                                </span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-medium text-slate-800">{{ $requirement->name }}</span>
                                        @if (! $requirement->is_required)
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200 font-medium">Optional</span>
                                        @endif
                                    </div>
                                    @if ($requirement->description)
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $requirement->description }}</p>
                                    @endif
                                    @if ($hasDoc)
                                        <div class="mt-1.5 flex flex-wrap gap-1.5">
                                            @foreach ($supplier->documents->where('requirement_id', $requirement->id) as $doc)
                                                <span class="inline-flex items-center gap-1 text-xs text-slate-600 bg-white border border-slate-200 rounded px-1.5 py-0.5">
                                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                    {{ $doc->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-rose-500">{{ $requirement->is_required ? 'Not yet uploaded' : 'No document uploaded' }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Supporting Documents</h3>
                @if ($supplier->documents->isEmpty())
                    <p class="text-sm text-slate-500">No documents uploaded.</p>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach ($supplier->documents as $doc)
                            <li class="py-2 flex items-center justify-between">
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="text-slate-400">📄</span>
                                    <div class="min-w-0">
                                        <div class="text-slate-700 truncate max-w-[260px]">{{ $doc->name }}</div>
                                        @if ($doc->requirement)
                                            <div class="text-xs text-emerald-600">{{ $doc->requirement->name }}</div>
                                        @endif
                                    </div>
                                    <span class="text-xs text-slate-400">{{ round($doc->size / 1024, 1) }} KB</span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('supplier-documents.download', $doc) }}" class="text-xs text-emerald-600 font-medium">Download</a>
                                    <form method="POST" action="{{ route('supplier-documents.destroy', $doc) }}">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-500 font-medium">Remove</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @if ($supplier->contracts->isNotEmpty())
                <div class="hd-card p-6">
                    <h3 class="font-semibold text-slate-900 mb-4">Contracts</h3>
                    <div class="space-y-2">
                        @foreach ($supplier->contracts as $contract)
                            <a href="{{ route('contracts.show', $contract) }}" class="flex items-center justify-between p-3 rounded-lg bg-slate-50 hover:bg-slate-100">
                                <div>
                                    <div class="text-sm font-medium text-slate-800">{{ $contract->title }}</div>
                                    <div class="text-xs text-slate-500">{{ $contract->reference }} · {{ $contract->statusLabel() }}</div>
                                </div>
                                <span class="text-sm font-medium text-slate-700">{{ currentOrganization()->currency }} {{ number_format($contract->value, 0) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-3">Vetting Status</h3>
                @if ($supplier->status === 'approved')
                    <div class="flex items-center gap-3 text-sm text-emerald-700">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        Approved {{ $supplier->approved_at?->diffForHumans() }}
                    </div>
                    <div class="text-xs text-slate-400 mt-1">Approved by {{ $supplier->approver?->name ?? '—' }}</div>
                @else
                    <p class="text-sm text-slate-500 mb-4">This supplier is awaiting vetting and approval before participating in tenders.</p>
                    @if (auth()->user()->isApprover())
                        <form method="POST" action="{{ route('suppliers.status', $supplier) }}" class="space-y-3">
                            @csrf
                            <div>
                                <x-input-label for="status" value="Change Status" />
                                <select name="status" id="status" class="mt-1 w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                    @foreach (['approved', 'pending', 'suspended', 'blacklisted'] as $s)
                                        <option value="{{ $s }}" @selected($supplier->status === $s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <textarea name="notes" rows="2" placeholder="Review notes..." class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">{{ $supplier->notes }}</textarea>
                            <button class="w-full px-4 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Update Status</button>
                        </form>
                    @endif
                @endif
            </div>

            @if ($supplier->bids->isNotEmpty())
                <div class="hd-card p-6">
                    <h3 class="font-semibold text-slate-900 mb-3">Bids Submitted</h3>
                    <div class="space-y-2">
                        @foreach ($supplier->bids as $bid)
                            <a href="{{ route('bids.show', $bid) }}" class="block p-3 rounded-lg bg-slate-50 hover:bg-slate-100">
                                <div class="text-sm font-medium text-slate-800">{{ $bid->tender?->title }}</div>
                                <div class="text-xs text-slate-500">{{ $bid->reference }} · {{ $bid->statusLabel() }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
