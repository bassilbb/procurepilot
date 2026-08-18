<x-app-layout>
    <x-slot name="header">Suppliers</x-slot>

    <x-page-header title="Supplier Register" description="Vetted, approved suppliers eligible for tendering">
        <x-slot name="actions">
            <form method="GET" action="{{ route('suppliers.index') }}" class="flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search suppliers..."
                       class="text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                <select name="status" class="text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="px-3 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-700">Filter</button>
            </form>
            <a href="{{ route('suppliers.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">+ Register Supplier</a>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse ($suppliers as $supplier)
            <a href="{{ route('suppliers.show', $supplier) }}" class="hd-card p-5 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 font-bold">
                        {{ strtoupper(substr($supplier->name, 0, 2)) }}
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full border font-medium {{ statusBadgeClass($supplier->status) }}">{{ $supplier->statusLabel() }}</span>
                </div>
                <h3 class="mt-3 font-semibold text-slate-900">{{ $supplier->name }}</h3>
                <p class="text-sm text-slate-500 mt-1">{{ $supplier->category?->name ?? '—' }}</p>
                <div class="mt-3 text-xs text-slate-400 space-y-1">
                    @if ($supplier->reg_number)
                        <div>Reg: {{ $supplier->reg_number }}</div>
                    @endif
                    @if ($supplier->country)
                        <div>{{ $supplier->country }}</div>
                    @endif
                </div>
            </a>
        @empty
            <div class="col-span-full">
                <x-empty-state title="No suppliers found" message="Register suppliers to begin building your procurement ecosystem."
                               icon="🏭">
                    <x-slot name="action">
                        <a href="{{ route('suppliers.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">Register Supplier</a>
                    </x-slot>
                </x-empty-state>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $suppliers->links() }}</div>
</x-app-layout>
