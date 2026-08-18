<x-app-layout>
    <x-slot name="header">Edit Procurement Plan</x-slot>

    <x-page-header :title="$plan->title" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Add Line Item</h3>
                <form method="POST" action="{{ route('plans.items.store', $plan) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div class="md:col-span-2">
                        <x-input-label for="title" value="Item Title" />
                        <x-text-input id="title" name="title" class="mt-1 w-full" required placeholder="e.g. Procurement of marine navigation equipment" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="2" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                    </div>
                    <div>
                        <x-input-label for="category_id" value="Category" />
                        <select id="category_id" name="category_id" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Select</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="quantity" value="Quantity" />
                        <x-text-input id="quantity" name="quantity" type="number" class="mt-1 w-full" value="1" min="1" />
                    </div>
                    <div>
                        <x-input-label for="estimated_cost" value="Estimated Cost (per unit)" />
                        <x-text-input id="estimated_cost" name="estimated_cost" type="number" step="0.01" class="mt-1 w-full" required value="0" min="0" />
                    </div>
                    <div>
                        <x-input-label for="method" value="Procurement Method" />
                        <select id="method" name="method" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="open_competitive">Open Competitive</option>
                            <option value="restricted">Restricted</option>
                            <option value="single_source">Single Source</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="priority" value="Priority" />
                        <select id="priority" name="priority" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach (['low', 'normal', 'high', 'critical'] as $p)
                                <option value="{{ $p }}" @selected($p === 'normal')>{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="expected_date" value="Expected Procurement Date" />
                        <input id="expected_date" name="expected_date" type="date" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <button class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Add Item</button>
                    </div>
                </form>
            </div>

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Line Items</h3>
                @if ($plan->items->isEmpty())
                    <p class="text-sm text-slate-500">No items added.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100">
                                    <th class="py-2 pr-3">Item</th>
                                    <th class="py-2 pr-3">Category</th>
                                    <th class="py-2 pr-3">Est. Cost</th>
                                    <th class="py-2 pr-3">Method</th>
                                    <th class="py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach ($plan->items as $item)
                                    <tr>
                                        <td class="py-3 pr-3 font-medium text-slate-800">{{ $item->title }}</td>
                                        <td class="py-3 pr-3 text-slate-500">{{ $item->category?->name ?? '—' }}</td>
                                        <td class="py-3 pr-3 font-medium">{{ currentOrganization()->currency }} {{ number_format($item->estimated_cost * $item->quantity, 0) }}</td>
                                        <td class="py-3 pr-3 text-xs">{{ $item->methodLabel() }}</td>
                                        <td class="py-3 text-right">
                                            <form method="POST" action="{{ route('plans.items.destroy', $item) }}" onsubmit="return confirm('Remove this item?')">
                                                @csrf @method('DELETE')
                                                <button class="text-xs text-red-500 hover:text-red-700">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Plan Settings</h3>
                <form method="POST" action="{{ route('plans.update', $plan) }}" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <x-input-label for="title" value="Title" />
                        <x-text-input id="title" name="title" class="mt-1 w-full" value="{{ $plan->title }}" required />
                    </div>
                    <div>
                        <x-input-label for="fiscal_year" value="Fiscal Year" />
                        <x-text-input id="fiscal_year" name="fiscal_year" class="mt-1 w-full" value="{{ $plan->fiscal_year }}" required />
                    </div>
                    <div>
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="3" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">{{ $plan->description }}</textarea>
                    </div>
                    <button class="w-full px-4 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-700">Save Plan</button>
                </form>
            </div>

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Summary</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd class="font-medium">{{ $plan->statusLabel() }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Items</dt><dd class="font-medium">{{ $plan->items->count() }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Estimated Budget</dt><dd class="font-medium">{{ currentOrganization()->currency }} {{ number_format($plan->totalEstimatedCost(), 0) }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
