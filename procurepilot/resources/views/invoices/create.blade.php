<x-app-layout>
    <x-slot name="header">Register Supplier Invoice</x-slot>

    <x-page-header title="Register Supplier Invoice" description="Record a supplier invoice for verification and three-way matching" />

    <form method="POST" action="{{ route('invoices.store') }}" x-data="invoiceForm()">
        @csrf

        <div class="hd-card p-6 mb-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">Invoice details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="supplier_id" :value="__('Supplier')" />
                    <select id="supplier_id" name="supplier_id" required class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Select supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="purchase_order_id" :value="__('Purchase Order (for 3-way match)')" />
                    <select id="purchase_order_id" name="purchase_order_id" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">— None —</option>
                        @foreach ($pos as $po)
                            <option value="{{ $po->id }}" @selected(old('purchase_order_id') == $po->id)>{{ $po->reference }} — {{ $po->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="contract_id" :value="__('Contract (optional)')" />
                    <select id="contract_id" name="contract_id" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">— None —</option>
                        @foreach ($contracts as $contract)
                            <option value="{{ $contract->id }}" @selected(old('contract_id') == $contract->id)>{{ $contract->reference }} — {{ $contract->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="invoice_date" :value="__('Invoice Date')" />
                    <x-text-input id="invoice_date" class="block mt-1 w-full" type="date" name="invoice_date" :value="old('invoice_date')" />
                </div>
                <div>
                    <x-input-label for="due_date" :value="__('Due Date')" />
                    <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date')" />
                </div>
                <div>
                    <x-input-label for="currency" :value="__('Currency')" />
                    <x-text-input id="currency" class="block mt-1 w-full" type="text" name="currency" :value="old('currency', 'NGN')" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="notes" :value="__('Notes')" />
                    <textarea id="notes" name="notes" rows="2" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm" placeholder="Any remarks">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="hd-card p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-900">Invoice lines</h3>
                <button type="button" @click="addItem()" class="px-3 py-1.5 text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg">+ Add line</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100">
                            <th class="py-2 pr-2">Description</th>
                            <th class="py-2 px-2 w-32">PO Line</th>
                            <th class="py-2 px-2 w-24">Qty</th>
                            <th class="py-2 px-2 w-24">Unit</th>
                            <th class="py-2 px-2 w-40">Unit Price (₦)</th>
                            <th class="py-2 pl-2 w-28">Total</th>
                            <th class="py-2 pl-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="py-2 pr-2"><input type="text" :name="`items[${index}][description]`" x-model="item.description" required class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm"></td>
                                <td class="py-2 px-2">
                                    <select :name="`items[${index}][po_item_id]`" x-model="item.po_item_id" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                        <option value="">—</option>
                                        @foreach ($pos as $po)
                                            @foreach ($po->items as $poItem)
                                                <option value="{{ $poItem->id }}">{{ $po->reference }} · {{ $poItem->description }}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-2 px-2"><input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" min="0" step="0.01" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm"></td>
                                <td class="py-2 px-2"><input type="text" :name="`items[${index}][unit]`" x-model="item.unit" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm"></td>
                                <td class="py-2 px-2"><input type="number" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" min="0" step="0.01" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm"></td>
                                <td class="py-2 pl-2 font-medium text-slate-700" x-text="lineTotal(item)"></td>
                                <td class="py-2 pl-2 text-right">
                                    <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <p x-show="items.length === 0" x-cloak class="text-sm text-slate-400 text-center py-6">Add at least one line.</p>
            </div>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="subtotal" :value="__('Subtotal (₦)')" />
                    <x-text-input id="subtotal" class="block mt-1 w-full" type="number" name="subtotal" x-model.number="subtotal" step="0.01" min="0" required />
                </div>
                <div>
                    <x-input-label for="tax_amount" :value="__('Tax (₦)')" />
                    <x-text-input id="tax_amount" class="block mt-1 w-full" type="number" name="tax_amount" x-model.number="tax_amount" step="0.01" min="0" />
                </div>
                <div>
                    <x-input-label :value="__('Total (₦)')" />
                    <div class="mt-1 px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-200 font-bold text-slate-900" x-text="total()">0.00</div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('invoices.index') }}" class="px-5 py-2.5 text-sm text-slate-600 hover:text-slate-800">Cancel</a>
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Register Invoice</button>
        </div>
    </form>

    <script>
        function invoiceForm() {
            return {
                items: [{ description: '', po_item_id: '', quantity: 1, unit: '', unit_price: 0 }],
                subtotal: 0,
                tax_amount: 0,
                addItem() {
                    this.items.push({ description: '', po_item_id: '', quantity: 1, unit: '', unit_price: 0 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                lineTotal(item) {
                    return (parseFloat(item.quantity) * parseFloat(item.unit_price || 0)).toLocaleString('en-NG', { minimumFractionDigits: 2 });
                },
                total() {
                    return (parseFloat(this.subtotal || 0) + parseFloat(this.tax_amount || 0)).toLocaleString('en-NG', { minimumFractionDigits: 2 });
                },
            };
        }
    </script>
</x-app-layout>
