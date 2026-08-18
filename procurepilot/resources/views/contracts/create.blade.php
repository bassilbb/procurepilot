<x-app-layout>
    <x-slot name="header">New Contract</x-slot>

    <x-page-header title="Create Contract" description="Register a contract with an approved supplier" />

    <form method="POST" action="{{ route('contracts.store') }}" class="hd-card p-6 max-w-3xl">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <x-input-label for="title" value="Contract Title" />
                <x-text-input id="title" name="title" class="mt-1 w-full" required placeholder="e.g. Supply and delivery of marine spare parts" value="{{ old('title') }}" />
            </div>
            <div class="md:col-span-2">
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description" rows="2" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('description') }}</textarea>
            </div>
            <div>
                <x-input-label for="supplier_id" value="Supplier" />
                <select id="supplier_id" name="supplier_id" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" required>
                    <option value="">Select approved supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="tender_id" value="Linked Tender (optional)" />
                <select id="tender_id" name="tender_id" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">None</option>
                    @foreach ($tenders as $tender)
                        <option value="{{ $tender->id }}" @selected(old('tender_id') == $tender->id)>{{ $tender->reference }} — {{ $tender->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="value" value="Contract Value" />
                <x-text-input id="value" name="value" type="number" step="0.01" min="0" class="mt-1 w-full" required value="{{ old('value') }}" />
            </div>
            <div>
                <x-input-label for="currency" value="Currency" />
                <x-text-input id="currency" name="currency" class="mt-1 w-full" value="{{ old('currency', 'NGN') }}" maxlength="10" />
            </div>
            <div>
                <x-input-label for="start_date" value="Start Date" />
                <input id="start_date" name="start_date" type="date" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" value="{{ old('start_date') }}" required />
            </div>
            <div>
                <x-input-label for="end_date" value="End Date" />
                <input id="end_date" name="end_date" type="date" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" value="{{ old('end_date') }}" required />
            </div>
            <div class="md:col-span-2">
                <x-input-label for="payment_terms" value="Payment Terms" />
                <x-text-input id="payment_terms" name="payment_terms" class="mt-1 w-full" value="{{ old('payment_terms', 'Net 30') }}" placeholder="e.g. Net 30, 50% advance" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('contracts.index') }}" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Cancel</a>
            <button class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create Contract</button>
        </div>
    </form>
</x-app-layout>
