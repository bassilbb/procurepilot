<x-app-layout>
    <x-slot name="header">Register Supplier</x-slot>

    <x-page-header title="Supplier Registration" description="Complete the vetting dossier. Suppliers are approved after compliance review." />

    <form method="POST" action="{{ route('suppliers.store') }}" enctype="multipart/form-data" class="hd-card p-6 max-w-3xl">
        @csrf

        <h3 class="font-semibold text-slate-900 mb-4">Company Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <x-input-label for="name" value="Company Name" />
                <x-text-input id="name" name="name" class="mt-1 w-full" required value="{{ old('name') }}" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="reg_number" value="Registration / RC Number" />
                <x-text-input id="reg_number" name="reg_number" class="mt-1 w-full" value="{{ old('reg_number') }}" placeholder="RC 123456" />
            </div>

            <div>
                <x-input-label for="tax_id" value="Tax Identification Number" />
                <x-text-input id="tax_id" name="tax_id" class="mt-1 w-full" value="{{ old('tax_id') }}" />
            </div>

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" name="email" type="email" class="mt-1 w-full" value="{{ old('email') }}" />
            </div>

            <div>
                <x-input-label for="phone" value="Phone" />
                <x-text-input id="phone" name="phone" class="mt-1 w-full" value="{{ old('phone') }}" />
            </div>

            <div class="md:col-span-2">
                <x-input-label for="address" value="Registered Address" />
                <textarea id="address" name="address" rows="2" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('address') }}</textarea>
            </div>

            <div>
                <x-input-label for="country" value="Country" />
                <x-text-input id="country" name="country" class="mt-1 w-full" value="{{ old('country', 'Nigeria') }}" />
            </div>

            <div>
                <x-input-label for="category_id" value="Category" />
                <select id="category_id" name="category_id" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Select category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <h3 class="font-semibold text-slate-900 mt-8 mb-4">Bank Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-3">
                <x-input-label for="bank_account_name" value="Account Name" />
                <x-text-input id="bank_account_name" name="bank_account_name" class="mt-1 w-full" value="{{ old('bank_account_name') }}" />
            </div>
            <div>
                <x-input-label for="bank_name" value="Bank" />
                <x-text-input id="bank_name" name="bank_name" class="mt-1 w-full" value="{{ old('bank_name') }}" />
            </div>
            <div class="md:col-span-2">
                <x-input-label for="bank_account_number" value="Account Number" />
                <x-text-input id="bank_account_number" name="bank_account_number" class="mt-1 w-full" value="{{ old('bank_account_number') }}" />
            </div>
        </div>

        <h3 class="font-semibold text-slate-900 mt-8 mb-4">Compliance & Certifications</h3>
        <div class="grid grid-cols-1 gap-4">
            <div>
                <x-input-label for="certifications" value="Certifications (ISO, NOA, e.t.c.)" />
                <textarea id="certifications" name="certifications" rows="2" placeholder="ISO 9001, NAFDAC, ITF Certificate of Compliance..." class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('certifications') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-between mt-8 mb-4">
            <h3 class="font-semibold text-slate-900">Required Documents</h3>
            <a href="{{ route('public.requirements.pdf') }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 hover:text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Download requirements checklist (PDF)
            </a>
        </div>
        <div class="space-y-3">
            @forelse ($requirements as $requirement)
                <div class="rounded-xl border {{ $requirement->is_required ? 'border-slate-200 bg-slate-50/60' : 'border-dashed border-slate-200' }} p-4">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold {{ $requirement->is_required ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-500' }}">{{ $loop->iteration }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-medium text-slate-900 text-sm">{{ $requirement->name }}</h4>
                                @if ($requirement->is_required)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-rose-50 text-rose-600 border border-rose-200 font-medium">Required</span>
                                @else
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200 font-medium">Optional</span>
                                @endif
                            </div>
                            @if ($requirement->description)
                                <p class="text-xs text-slate-500 mt-0.5">{{ $requirement->description }}</p>
                            @endif
                            <input type="file" name="documents[{{ $requirement->id }}][]" {{ $requirement->is_required ? 'required' : '' }} multiple
                                   class="mt-2 w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No document requirements configured. The admin can set them in Settings → Supplier Registration Requirements.</p>
            @endforelse

            <div class="rounded-xl border border-dashed border-slate-200 p-4">
                <x-input-label for="other_documents" value="Additional Supporting Documents (optional)" />
                <input id="other_documents" name="other_documents[]" type="file" multiple class="mt-2 w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
            </div>

            <div>
                <x-input-label for="notes" value="Notes" />
                <textarea id="notes" name="notes" rows="2" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('suppliers.index') }}" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Cancel</a>
            <button class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Register Supplier</button>
        </div>
    </form>
</x-app-layout>
