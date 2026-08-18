<x-app-layout>
    <x-slot name="header">New Purchase Order</x-slot>

    <x-page-header title="Create Purchase Order" description="Issue an order against an approved supplier" />

    <form method="POST" action="{{ route('purchase-orders.store') }}" class="hd-card p-6 max-w-3xl">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <x-input-label for="title" value="Order Title" />
                <x-text-input id="title" name="title" class="mt-1 w-full" required placeholder="e.g. Order of container tracking devices" value="{{ old('title') }}" />
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
                <x-input-label for="contract_id" value="Linked Contract (optional)" />
                <select id="contract_id" name="contract_id" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">None</option>
                    @foreach ($contracts as $contract)
                        <option value="{{ $contract->id }}" @selected(old('contract_id') == $contract->id)>{{ $contract->reference }} — {{ $contract->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="order_date" value="Order Date" />
                <input id="order_date" name="order_date" type="date" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" value="{{ old('order_date', date('Y-m-d')) }}" />
            </div>
            <div>
                <x-input-label for="expected_delivery" value="Expected Delivery" />
                <input id="expected_delivery" name="expected_delivery" type="date" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" value="{{ old('expected_delivery') }}" />
            </div>
            <div class="md:col-span-2">
                <x-input-label for="currency" value="Currency" />
                <x-text-input id="currency" name="currency" class="mt-1 w-full" value="{{ old('currency', 'NGN') }}" maxlength="10" />
            </div>
        </div>

        <h3 class="font-semibold text-slate-900 mt-8 mb-4">Order Items</h3>
        <div id="items-wrap" class="space-y-3">
            <div class="item-row grid grid-cols-12 gap-2 items-center">
                <div class="col-span-5"><input name="items[0][description]" placeholder="Item description" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                <div class="col-span-2"><input name="items[0][quantity]" type="number" min="0.01" step="0.01" placeholder="Qty" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                <div class="col-span-1"><input name="items[0][unit]" placeholder="Unit" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                <div class="col-span-3"><input name="items[0][unit_price]" type="number" min="0" step="0.01" placeholder="Unit price" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                <div class="col-span-1 text-right"><button type="button" onclick="this.closest('.item-row').remove()" class="text-red-400 hover:text-red-600 text-xs">✕</button></div>
            </div>
        </div>
        <button type="button" onclick="addPoItemRow()" class="mt-2 text-sm text-emerald-600 hover:text-emerald-700 font-medium">+ Add item</button>

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('purchase-orders.index') }}" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Cancel</a>
            <button class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create Purchase Order</button>
        </div>
    </form>

    @push('head')
    <script>
        let poItemIndex = 1;
        function addPoItemRow() {
            const wrap = document.getElementById('items-wrap');
            const div = document.createElement('div');
            div.className = 'item-row grid grid-cols-12 gap-2 items-center';
            div.innerHTML = `
                <div class="col-span-5"><input name="items[${poItemIndex}][description]" placeholder="Item description" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                <div class="col-span-2"><input name="items[${poItemIndex}][quantity]" type="number" min="0.01" step="0.01" placeholder="Qty" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                <div class="col-span-1"><input name="items[${poItemIndex}][unit]" placeholder="Unit" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                <div class="col-span-3"><input name="items[${poItemIndex}][unit_price]" type="number" min="0" step="0.01" placeholder="Unit price" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                <div class="col-span-1 text-right"><button type="button" onclick="this.closest('.item-row').remove()" class="text-red-400 hover:text-red-600 text-xs">✕</button></div>`;
            wrap.appendChild(div);
            poItemIndex++;
        }
    </script>
    @endpush
</x-app-layout>
