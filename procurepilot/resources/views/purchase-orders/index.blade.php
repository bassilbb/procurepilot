<x-app-layout>
    <x-slot name="header">Purchase Orders</x-slot>

    <x-page-header title="Purchase Orders" description="Orders issued to suppliers against contracts">
        <x-slot name="actions">
            <form method="GET" action="{{ route('purchase-orders.index') }}" class="flex gap-2">
                <select name="status" class="text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
                <button class="px-3 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Filter</button>
            </form>
            <a href="{{ route('purchase-orders.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">+ New Purchase Order</a>
        </x-slot>
    </x-page-header>

    <div class="hd-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4">Reference</th>
                        <th class="py-3 px-4">Title</th>
                        <th class="py-3 px-4">Supplier</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Expected Delivery</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($orders as $po)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono text-xs text-slate-500">{{ $po->reference }}</td>
                            <td class="py-3 px-4"><a href="{{ route('purchase-orders.show', $po) }}" class="font-medium text-slate-800 hover:text-emerald-700">{{ $po->title }}</a></td>
                            <td class="py-3 px-4 text-slate-700">{{ $po->supplier?->name }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-900">{{ $po->currency }} {{ number_format($po->total, 0) }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $po->expected_delivery?->format('d M Y') ?? '—' }}</td>
                            <td class="py-3 px-4"><span class="text-xs px-2 py-1 rounded-full border font-medium {{ statusBadgeClass($po->status) }}">{{ $po->statusLabel() }}</span></td>
                            <td class="py-3 px-4 text-right"><a href="{{ route('purchase-orders.show', $po) }}" class="text-xs text-emerald-600 font-medium">View</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">No purchase orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6">{{ $orders->links() }}</div>
</x-app-layout>
