<x-app-layout>
    <x-slot name="header">New Procurement Request</x-slot>

    <x-page-header title="New Procurement Request" description="Describe what your department needs and why" />

    <form method="POST" action="{{ route('requests.store') }}" x-data="requestForm()">
        @csrf

        <div class="hd-card p-6 mb-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">Request details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-input-label for="title" :value="__('Request Title')" />
                    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required placeholder="e.g. Supply of office IT equipment" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="justification" :value="__('Justification')" />
                    <textarea id="justification" name="justification" rows="3" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm" placeholder="Why is this procurement needed?">{{ old('justification') }}</textarea>
                </div>
                <div>
                    <x-input-label for="department_id" :value="__('Department')" />
                    <select id="department_id" name="department_id" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Select department</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" @selected(old('department_id') == $dept->id)>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="category_id" :value="__('Category')" />
                    <select id="category_id" name="category_id" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Select category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="budget_code" :value="__('Budget Code')" />
                    <x-text-input id="budget_code" class="block mt-1 w-full" type="text" name="budget_code" :value="old('budget_code')" placeholder="e.g. CAP-2026-0041" />
                </div>
                <div>
                    <x-input-label for="priority" :value="__('Priority')" />
                    <select id="priority" name="priority" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach (['low', 'normal', 'high', 'critical'] as $p)
                            <option value="{{ $p }}" @selected(old('priority', 'normal') == $p)>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="required_date" :value="__('Required By')" />
                    <x-text-input id="required_date" class="block mt-1 w-full" type="date" name="required_date" :value="old('required_date')" />
                </div>
                <div>
                    <x-input-label for="currency" :value="__('Currency')" />
                    <x-text-input id="currency" class="block mt-1 w-full" type="text" name="currency" :value="old('currency', 'NGN')" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="notes" :value="__('Notes')" />
                    <textarea id="notes" name="notes" rows="2" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm" placeholder="Any additional information">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="hd-card p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-900">Items</h3>
                <button type="button" @click="addItem()" class="px-3 py-1.5 text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg">+ Add item</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100">
                            <th class="py-2 pr-2">Description</th>
                            <th class="py-2 px-2 w-24">Qty</th>
                            <th class="py-2 px-2 w-24">Unit</th>
                            <th class="py-2 px-2 w-40">Unit Cost (₦)</th>
                            <th class="py-2 pl-2 w-28">Total</th>
                            <th class="py-2 pl-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="py-2 pr-2"><input type="text" :name="`items[${index}][description]`" x-model="item.description" required class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" placeholder="Item description"></td>
                                <td class="py-2 px-2"><input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" min="1" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm"></td>
                                <td class="py-2 px-2"><input type="text" :name="`items[${index}][unit]`" x-model="item.unit" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" placeholder="pcs"></td>
                                <td class="py-2 px-2"><input type="number" :name="`items[${index}][estimated_unit_cost]`" x-model.number="item.estimated_unit_cost" min="0" step="0.01" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm"></td>
                                <td class="py-2 pl-2 font-medium text-slate-700" x-text="itemTotal(item)"></td>
                                <td class="py-2 pl-2 text-right">
                                    <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <p x-show="items.length === 0" x-cloak class="text-sm text-slate-400 text-center py-6">Add at least one item to this request.</p>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="text-sm text-slate-500">Estimated total: <span class="font-bold text-slate-900" x-text="grandTotal()">0.00</span></div>
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Save Request</button>
        </div>
    </form>

    <script>
        function requestForm() {
            return {
                items: [{ description: '', quantity: 1, unit: '', estimated_unit_cost: 0 }],
                addItem() {
                    this.items.push({ description: '', quantity: 1, unit: '', estimated_unit_cost: 0 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                itemTotal(item) {
                    return (parseFloat(item.quantity) * parseFloat(item.estimated_unit_cost || 0)).toLocaleString('en-NG', { minimumFractionDigits: 2 });
                },
                grandTotal() {
                    return this.items.reduce((sum, i) => sum + (parseFloat(i.quantity) * parseFloat(i.estimated_unit_cost || 0)), 0).toLocaleString('en-NG', { minimumFractionDigits: 2 });
                },
            };
        }
    </script>
</x-app-layout>
