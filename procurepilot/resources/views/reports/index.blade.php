<x-app-layout>
    <x-slot name="header">Reports</x-slot>

    <x-page-header title="Reports Center" description="Procurement performance and spend analytics">
        <x-slot name="actions">
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.export', ['format' => 'excel'] + request()->query()) }}" class="btn-3d btn-3d-edit">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </a>
                <a href="{{ route('reports.export', ['format' => 'csv'] + request()->query()) }}" class="btn-3d btn-3d-edit">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 12h6m-3-3v6"/></svg>
                    CSV
                </a>
                <a href="{{ route('reports.export', ['format' => 'pdf'] + request()->query()) }}" class="btn-3d btn-3d-add">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    PDF
                </a>
            </div>
        </x-slot>
    </x-page-header>

    <div class="relative mb-8 rounded-2xl overflow-hidden border border-slate-200"
         style="background: linear-gradient(120deg, #f0fdf4 0%, #ecfdf5 35%, #eef2ff 100%); box-shadow: 0 1px 0 rgba(255,255,255,.9) inset, 0 8px 24px -8px rgba(15,23,42,.12);">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(circle at 20% 20%, #059669 0, transparent 45%), radial-gradient(circle at 80% 30%, #6366f1 0, transparent 40%), radial-gradient(circle at 60% 90%, #10b981 0, transparent 45%);"></div>
        <div class="relative px-6 py-5">
            <div class="flex items-center gap-2.5 mb-4">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 6px 14px -4px rgba(16,185,129,.6), inset 0 1px 0 rgba(255,255,255,.35);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold tracking-tight text-slate-900">Report filters</h2>
                    <p class="text-xs text-slate-600">Query across date ranges, statuses and other criteria · export to Excel, PDF or CSV</p>
                </div>
            </div>
            <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <div>
                    <x-input-label :value="__('Report type')" />
                    <select name="type" onchange="this.form.submit()" class="mt-1 w-full text-sm rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach ($reportTypes as $key => $label)
                            <option value="{{ $key }}" @selected($filters['type'] === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label :value="__('From')" />
                    <x-text-input class="mt-1 w-full text-sm" type="date" name="date_from" :value="$filters['date_from']" />
                </div>
                <div>
                    <x-input-label :value="__('To')" />
                    <x-text-input class="mt-1 w-full text-sm" type="date" name="date_to" :value="$filters['date_to']" />
                </div>
                <div>
                    <x-input-label :value="__('Status')" />
                    <select name="status" class="mt-1 w-full text-sm rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">All</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label :value="__('Category')" />
                    <select name="category_id" class="mt-1 w-full text-sm rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">All</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected($filters['category_id'] == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label :value="__('Supplier')" />
                    <select name="supplier_id" class="mt-1 w-full text-sm rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">All</option>
                        @foreach ($suppliers as $sup)
                            <option value="{{ $sup->id }}" @selected($filters['supplier_id'] == $sup->id)>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if (in_array($filters['type'], ['requests', 'budgets'], true))
                    <div>
                        <x-input-label :value="__('Department')" />
                        <select name="department_id" class="mt-1 w-full text-sm rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">All</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" @selected($filters['department_id'] == $dept->id)>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @if ($filters['type'] === 'budgets')
                    <div>
                        <x-input-label :value="__('Fiscal year')" />
                        <x-text-input class="mt-1 w-full text-sm" type="number" name="fiscal_year" :value="$filters['fiscal_year']" placeholder="e.g. 2026" />
                    </div>
                @endif
                <div class="flex items-end gap-2 {{ in_array($filters['type'], ['requests', 'budgets'], true) ? '' : '' }}">
                    <button type="submit" class="btn-3d btn-3d-add">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Apply
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn-3d btn-3d-edit">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if ($filters['type'] === 'overview')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <x-stat-card label="Requests" :value="$kpi['requests']" tone="emerald" icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            <x-stat-card label="Request Value" value="₦ {{ number_format($kpi['requests_value'], 0) }}" tone="blue" icon="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            <x-stat-card label="Contract Value (Active)" value="₦ {{ number_format($kpi['contract_value'], 0) }}" tone="amber" icon="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            <x-stat-card label="Invoices Paid" value="₦ {{ number_format($kpi['paid'], 0) }}" tone="slate" icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </div>

        @php $spend = $data['extra']['spendByCategory']; @endphp
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="lg:col-span-2 workflow-card p-6">
                <h3 class="workflow-card-title mb-4"><span class="badge-dot"></span> Spend by category (active contracts)</h3>
                @php $maxSpend = $spend->first()['total'] ?? 1; @endphp
                @if ($spend->isEmpty())
                    <p class="text-sm text-slate-400 py-6 text-center">No contract spend yet.</p>
                @else
                    <div class="space-y-4">
                        @foreach ($spend as $sp)
                            <div>
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="font-medium text-slate-700">{{ $sp['name'] }}</span>
                                    <span class="text-slate-500">₦ {{ number_format($sp['total'], 0) }}</span>
                                </div>
                                <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500" style="width: {{ round(($sp['total'] / $maxSpend) * 100) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="workflow-card p-6">
                <h3 class="workflow-card-title mb-4"><span class="badge-dot" style="background:linear-gradient(135deg,#6366f1,#4f46e5); box-shadow:0 0 0 3px rgba(99,102,241,.18), 0 2px 6px rgba(99,102,241,.4);"></span> Budget utilization</h3>
                @php $budgetSummary = $data['extra']['budgetSummary']; @endphp
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-slate-500">Allocated</span><span class="font-semibold">₦ {{ number_format($budgetSummary['allocated'], 0) }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Committed</span><span class="font-semibold text-blue-600">₦ {{ number_format($budgetSummary['committed'], 0) }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Spent</span><span class="font-semibold text-amber-600">₦ {{ number_format($budgetSummary['spent'], 0) }}</span></div>
                    <div class="flex justify-between border-t border-slate-100 pt-3"><span class="text-slate-500">Remaining</span><span class="font-bold text-emerald-600">₦ {{ number_format($budgetSummary['allocated'] - $budgetSummary['committed'] - $budgetSummary['spent'], 0) }}</span></div>
                    @php $utilPct = $budgetSummary['allocated'] > 0 ? round((($budgetSummary['committed'] + $budgetSummary['spent']) / $budgetSummary['allocated']) * 100) : 0; @endphp
                    <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $utilPct > 90 ? 'bg-red-500' : ($utilPct > 70 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min(100, $utilPct) }}%"></div>
                    </div>
                    <p class="text-xs text-slate-400">{{ $utilPct }}% utilized</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            @foreach (['requests', 'tenders', 'contracts'] as $key)
                <div class="workflow-card p-6">
                    <h3 class="workflow-card-title mb-4"><span class="badge-dot"></span> {{ ucfirst($key) }} by status</h3>
                    <div class="space-y-2 text-sm">
                        @forelse ($data['extra']['statusSummary'][$key] as $row)
                            <div class="flex justify-between items-center">
                                <span class="capitalize text-slate-500">{{ str_replace('_', ' ', $row->status) }}</span>
                                <span class="font-semibold text-slate-800">{{ $row->count }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400">No data.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="workflow-card">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="workflow-card-title"><span class="badge-dot"></span> {{ $data['title'] }}
                    <span class="ml-2 text-xs font-semibold text-slate-400">({{ count($data['rows']) }} records)</span>
                </h3>
            </div>
            <div class="overflow-x-auto">
                @if (empty($data['rows']))
                    <p class="py-12 text-center text-sm text-slate-400">No records match the selected criteria.</p>
                @else
                    <table class="w-full text-sm wf-table">
                        <thead>
                            <tr class="text-left uppercase text-slate-400 border-b border-slate-200/70">
                                @foreach ($data['columns'] as $col)
                                    <th class="py-3 px-6">{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($data['rows'] as $row)
                                <tr>
                                    @foreach ($row as $cell)
                                        <td class="py-3 px-6 text-slate-600">{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @endif

    <div class="workflow-card mt-6">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="workflow-card-title"><span class="badge-dot" style="background:linear-gradient(135deg,#64748b,#475569); box-shadow:0 0 0 3px rgba(100,116,139,.18), 0 2px 6px rgba(100,116,139,.4);"></span> Recent activity</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm wf-table">
                <thead>
                    <tr class="text-left uppercase text-slate-400 border-b border-slate-200/70">
                        <th class="py-3 px-6">Time</th>
                        <th class="py-3 px-4">User</th>
                        <th class="py-3 px-4">Action</th>
                        <th class="py-3 px-4">Target</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentAudits as $log)
                        <tr>
                            <td class="py-3 px-6 text-xs text-slate-400">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="py-3 px-4 text-slate-700">{{ $log->user?->name ?? 'System' }}</td>
                            <td class="py-3 px-4"><span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-medium">{{ str_replace('_', ' ', $log->action) }}</span></td>
                            <td class="py-3 px-4 text-xs text-slate-500">{{ class_basename($log->auditable_type ?? '') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-8 text-center text-slate-400">No activity yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
