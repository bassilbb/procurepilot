<x-app-layout>
    <x-slot name="header">Edit Supplier</x-slot>

    <x-page-header :title="'Edit ' . $supplier->name" />

    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" enctype="multipart/form-data" class="hd-card p-6 max-w-3xl">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <x-input-label for="name" value="Company Name" />
                <x-text-input id="name" name="name" class="mt-1 w-full" required value="{{ old('name', $supplier->name) }}" />
            </div>
            <div><x-input-label for="reg_number" value="Registration Number" /><x-text-input id="reg_number" name="reg_number" class="mt-1 w-full" value="{{ old('reg_number', $supplier->reg_number) }}" /></div>
            <div><x-input-label for="tax_id" value="Tax ID" /><x-text-input id="tax_id" name="tax_id" class="mt-1 w-full" value="{{ old('tax_id', $supplier->tax_id) }}" /></div>
            <div><x-input-label for="email" value="Email" /><x-text-input id="email" name="email" type="email" class="mt-1 w-full" value="{{ old('email', $supplier->email) }}" /></div>
            <div><x-input-label for="phone" value="Phone" /><x-text-input id="phone" name="phone" class="mt-1 w-full" value="{{ old('phone', $supplier->phone) }}" /></div>
            <div class="md:col-span-2">
                <x-input-label for="address" value="Address" />
                <textarea id="address" name="address" rows="2" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('address', $supplier->address) }}</textarea>
            </div>
            <div><x-input-label for="country" value="Country" /><x-text-input id="country" name="country" class="mt-1 w-full" value="{{ old('country', $supplier->country) }}" /></div>
            <div>
                <x-input-label for="category_id" value="Category" />
                <select id="category_id" name="category_id" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Select category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $supplier->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><x-input-label for="bank_account_name" value="Account Name" /><x-text-input id="bank_account_name" name="bank_account_name" class="mt-1 w-full" value="{{ old('bank_account_name', $supplier->bank_account_name) }}" /></div>
            <div><x-input-label for="bank_name" value="Bank" /><x-text-input id="bank_name" name="bank_name" class="mt-1 w-full" value="{{ old('bank_name', $supplier->bank_name) }}" /></div>
            <div><x-input-label for="bank_account_number" value="Account Number" /><x-text-input id="bank_account_number" name="bank_account_number" class="mt-1 w-full" value="{{ old('bank_account_number', $supplier->bank_account_number) }}" /></div>
            <div class="md:col-span-2">
                <x-input-label for="certifications" value="Certifications" />
                <textarea id="certifications" name="certifications" rows="2" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('certifications', $supplier->certifications) }}</textarea>
            </div>
        </div>

        <h3 class="font-semibold text-slate-900 mt-8 mb-4">Required Documents</h3>
        <div class="space-y-3">
            @forelse ($requirements as $requirement)
                @php
                    $uploaded = $supplier->documents->where('requirement_id', $requirement->id);
                @endphp
                <div class="rounded-xl border {{ $requirement->is_required ? 'border-slate-200 bg-slate-50/60' : 'border-dashed border-slate-200' }} p-4">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold {{ $uploaded->isNotEmpty() ? 'bg-emerald-100 text-emerald-700' : ($requirement->is_required ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-500') }}">{{ $loop->iteration }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-medium text-slate-900 text-sm">{{ $requirement->name }}</h4>
                                @if ($uploaded->isNotEmpty())
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-200 font-medium">{{ $uploaded->count() }} uploaded</span>
                                @elseif ($requirement->is_required)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-rose-50 text-rose-600 border border-rose-200 font-medium">Missing</span>
                                @else
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200 font-medium">Optional</span>
                                @endif
                            </div>
                            @if ($requirement->description)
                                <p class="text-xs text-slate-500 mt-0.5">{{ $requirement->description }}</p>
                            @endif
                            @if ($uploaded->isNotEmpty())
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($uploaded as $doc)
                                        <span class="inline-flex items-center gap-1.5 text-xs bg-white border border-slate-200 rounded-md px-2 py-1">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span class="text-slate-700 max-w-[180px] truncate">{{ $doc->name }}</span>
                                            <a href="{{ route('supplier-documents.download', $doc) }}" class="text-emerald-600 hover:text-emerald-700 font-medium">↓</a>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            <input type="file" name="documents[{{ $requirement->id }}][]" multiple
                                   class="mt-2 w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No document requirements configured.</p>
            @endforelse

            <div class="rounded-xl border border-dashed border-slate-200 p-4">
                <x-input-label for="other_documents" value="Additional Supporting Documents (optional)" />
                <input id="other_documents" name="other_documents[]" type="file" multiple class="mt-2 w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('suppliers.show', $supplier) }}" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Cancel</a>
            <button class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Save Changes</button>
        </div>
    </form>
</x-app-layout>
