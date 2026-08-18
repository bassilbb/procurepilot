<x-app-layout>
    <x-slot name="header">New Tender</x-slot>

    <x-page-header title="Create Tender / RFQ" description="Configure the invitation, scope, and evaluation rules" />

    <form method="POST" action="{{ route('tenders.store') }}" class="hd-card p-6 max-w-3xl">
        @csrf

        <h3 class="font-semibold text-slate-900 mb-4">Tender Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <x-input-label for="title" value="Tender Title" />
                <x-text-input id="title" name="title" class="mt-1 w-full" required placeholder="e.g. Supply of marine diesel fuel for port operations" value="{{ old('title') }}" />
            </div>
            <div class="md:col-span-2">
                <x-input-label for="description" value="Description / Scope of Work" />
                <textarea id="description" name="description" rows="3" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('description') }}</textarea>
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
            <div>
                <x-input-label for="type" value="Tender Type" />
                <select id="type" name="type" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="open" @selected(old('type', 'open') === 'open')>Open</option>
                    <option value="restricted" @selected(old('type') === 'restricted')>Restricted</option>
                </select>
            </div>
            <div>
                <x-input-label for="method" value="Procurement Method" />
                <select id="method" name="method" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="open_competitive" @selected(old('method', 'open_competitive') === 'open_competitive')>Open Competitive Bidding</option>
                    <option value="restricted" @selected(old('method') === 'restricted')>Restricted Bidding</option>
                    <option value="direct" @selected(old('method') === 'direct')>Direct Procurement</option>
                </select>
            </div>
            <div>
                <x-input-label for="evaluation_method" value="Evaluation Method" />
                <select id="evaluation_method" name="evaluation_method" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="weighted_score" @selected(old('evaluation_method', 'weighted_score') === 'weighted_score')>Weighted Score (Technical & Financial)</option>
                    <option value="lowest_price" @selected(old('evaluation_method') === 'lowest_price')>Lowest Price</option>
                    <option value="quality_cost" @selected(old('evaluation_method') === 'quality_cost')>Quality-Cost Ratio</option>
                </select>
            </div>
            <div>
                <x-input-label for="budget" value="Budget ({{ currentOrganization()->currency ?? 'NGN' }})" />
                <x-text-input id="budget" name="budget" type="number" step="0.01" min="0" class="mt-1 w-full" value="{{ old('budget') }}" />
            </div>
            <div>
                <x-input-label for="currency" value="Currency" />
                <x-text-input id="currency" name="currency" class="mt-1 w-full" value="{{ old('currency', currentOrganization()->currency ?? 'NGN') }}" maxlength="10" />
            </div>
            <div>
                <x-input-label for="closing_at" value="Submission Deadline" />
                <input id="closing_at" name="closing_at" type="datetime-local" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" value="{{ old('closing_at') }}" />
            </div>
            <div>
                <x-input-label for="opening_at" value="Bid Opening Date" />
                <input id="opening_at" name="opening_at" type="datetime-local" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" value="{{ old('opening_at') }}" />
            </div>
        </div>

        <h3 class="font-semibold text-slate-900 mt-8 mb-4">Line Items (Scope)</h3>
        <div id="items-wrap" class="space-y-3">
            <div class="item-row grid grid-cols-12 gap-2 items-center">
                <div class="col-span-5"><input name="items[0][description]" placeholder="Item description" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                <div class="col-span-2"><input name="items[0][quantity]" type="number" min="0.01" step="0.01" placeholder="Qty" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                <div class="col-span-2"><input name="items[0][unit]" placeholder="Unit" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                <div class="col-span-2"><input name="items[0][estimated_unit_price]" type="number" min="0" step="0.01" placeholder="Unit price" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                <div class="col-span-1 text-right"><button type="button" onclick="this.closest('.item-row').remove()" class="text-red-400 hover:text-red-600 text-xs">✕</button></div>
            </div>
        </div>
        <button type="button" onclick="addItemRow()" class="mt-2 text-sm text-emerald-600 hover:text-emerald-700 font-medium">+ Add item</button>

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('tenders.index') }}" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Cancel</a>
            <button class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create Tender</button>
        </div>
    </form>

    @push('head')
    <script>
        let itemIndex = 1;
        function addItemRow() {
            const wrap = document.getElementById('items-wrap');
            const div = document.createElement('div');
            div.className = 'item-row grid grid-cols-12 gap-2 items-center';
            div.innerHTML = `
                <div class="col-span-5"><input name="items[${itemIndex}][description]" placeholder="Item description" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                <div class="col-span-2"><input name="items[${itemIndex}][quantity]" type="number" min="0.01" step="0.01" placeholder="Qty" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                <div class="col-span-2"><input name="items[${itemIndex}][unit]" placeholder="Unit" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                <div class="col-span-2"><input name="items[${itemIndex}][estimated_unit_price]" type="number" min="0" step="0.01" placeholder="Unit price" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                <div class="col-span-1 text-right"><button type="button" onclick="this.closest('.item-row').remove()" class="text-red-400 hover:text-red-600 text-xs">✕</button></div>`;
            wrap.appendChild(div);
            itemIndex++;
        }
    </script>
    @endpush
</x-app-layout>
