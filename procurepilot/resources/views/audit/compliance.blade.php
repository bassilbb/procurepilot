<x-app-layout>
    <x-slot name="header">Compliance Dashboard</x-slot>

    <x-page-header title="Procurement Compliance" description="Alignment with the Public Procurement Act 2007 (Nigeria) & international best practice (World Bank, UN, ISO 20400)" />

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="Registered Suppliers" :value="$stats['suppliers']" tone="sky" href="{{ route('suppliers.index') }}" />
        <x-stat-card label="Vetted & Approved" :value="$stats['approved_suppliers']" tone="emerald" href="{{ route('suppliers.index') }}" />
        <x-stat-card label="Open Tenders" :value="$stats['open_tenders']" tone="violet" href="{{ route('tenders.index') }}" />
        <x-stat-card label="Active Contracts" :value="$stats['active_contracts']" tone="blue" href="{{ route('contracts.index') }}" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="hd-card p-6">
            <h3 class="font-semibold text-slate-900 mb-4">Compliance Framework</h3>
            <ul class="space-y-4 text-sm">
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">✓</span>
                    <div>
                        <div class="font-medium text-slate-800">Procurement Planning (§27 PPA 2007)</div>
                        <p class="text-slate-500 text-xs mt-0.5">Annual procurement plans approved by the Tenders Board before execution.</p>
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">✓</span>
                    <div>
                        <div class="font-medium text-slate-800">Open Competitive Bidding (§24 PPA 2007)</div>
                        <p class="text-slate-500 text-xs mt-0.5">Goods and works procured through open advertisement with defined thresholds.</p>
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">✓</span>
                    <div>
                        <div class="font-medium text-slate-800">Supplier Vetting & Certification</div>
                        <p class="text-slate-500 text-xs mt-0.5">All suppliers registered and vetted with CAC, TIN and compliance documents.</p>
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">✓</span>
                    <div>
                        <div class="font-medium text-slate-800">Tenders Board Approval (§34–40 PPA 2007)</div>
                        <p class="text-slate-500 text-xs mt-0.5">Awards require recommendation and formal approval by the Tenders Board.</p>
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">✓</span>
                    <div>
                        <div class="font-medium text-slate-800">Full Audit Trail</div>
                        <p class="text-slate-500 text-xs mt-0.5">Every action recorded immutably with user, timestamp and IP for auditability.</p>
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">✓</span>
                    <div>
                        <div class="font-medium text-slate-800">Sustainable Procurement (ISO 20400)</div>
                        <p class="text-slate-500 text-xs mt-0.5">Evaluation criteria encourage sustainability, local content and transparency.</p>
                    </div>
                </li>
            </ul>
        </div>

        <div class="space-y-6">
            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Procurement Statistics</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Total Tenders</dt><dd class="font-medium">{{ $stats['tenders'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Open / Published</dt><dd class="font-medium text-violet-600">{{ $stats['open_tenders'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Total Bids</dt><dd class="font-medium">{{ $stats['bids'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Contracts Registered</dt><dd class="font-medium">{{ $stats['contracts'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Active Contracts</dt><dd class="font-medium text-emerald-600">{{ $stats['active_contracts'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Purchase Orders</dt><dd class="font-medium">{{ $stats['pos'] }}</dd></div>
                    <div class="flex justify-between pt-2 border-t border-slate-100"><dt class="font-semibold text-slate-700">Supplier Approval Rate</dt><dd class="font-bold">{{ $stats['suppliers'] > 0 ? round(($stats['approved_suppliers'] / $stats['suppliers']) * 100) : 0 }}%</dd></div>
                </dl>
            </div>

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Transparency Indicators</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <div class="flex justify-between mb-1"><span class="text-slate-600">Open bidding tenders</span><span class="font-medium">N/A</span></div>
                        <div class="h-2 bg-slate-100 rounded-full"><div class="h-2 bg-emerald-500 rounded-full" style="width: 100%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1"><span class="text-slate-600">Audit trail coverage</span><span class="font-medium">100%</span></div>
                        <div class="h-2 bg-slate-100 rounded-full"><div class="h-2 bg-emerald-500 rounded-full" style="width: 100%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1"><span class="text-slate-600">Award decision documentation</span><span class="font-medium">N/A</span></div>
                        <div class="h-2 bg-slate-100 rounded-full"><div class="h-2 bg-emerald-500 rounded-full" style="width: 100%"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
