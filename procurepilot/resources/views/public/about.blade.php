<x-public-layout title="About Us">
    <x-public-hero
        eyebrow="Who We Are"
        title="Procurement governance for global trade"
        subtitle="ProcurePilot is an enterprise procurement management platform purpose-built for port regulators, shippers' councils and trade organizations that must spend public money with total transparency and accountability." />

    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-2xl font-bold text-white">Our purpose</h2>
                <p class="mt-4 text-slate-400 leading-relaxed">
                    Every dollar, naira or pound spent on procurement should be traceable. ProcurePilot was created to give
                    procurement officers, Tenders Boards and finance teams a single source of truth — from the moment a
                    department submits a request, through competitive tendering and evaluation, to contract award,
                    purchase orders, goods receipt and payment.
                </p>
                <p class="mt-4 text-slate-400 leading-relaxed">
                    The platform operationalizes the discipline of the Nigerian Public Procurement Act 2007 and
                    international frameworks such as ISO 20400 and the World Bank Procurement Framework, so that best
                    practice is not a policy document but the way the system works.
                </p>
                <div class="mt-6 grid grid-cols-3 gap-4">
                    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-emerald-400">End-to-end</div>
                        <div class="text-xs text-slate-500 mt-1">request → payment</div>
                    </div>
                    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-emerald-400">Full audit</div>
                        <div class="text-xs text-slate-500 mt-1">every action logged</div>
                    </div>
                    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-emerald-400">PPA 2007</div>
                        <div class="text-xs text-slate-500 mt-1">compliant by design</div>
                    </div>
                </div>
            </div>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8">
                <h3 class="text-white font-bold">Key benefits</h3>
                <ul class="mt-5 space-y-4">
                    @php
                        $benefits = [
                            ['Transparency', 'Sealed bids, weighted scoring and documented award rationale keep every decision defensible.'],
                            ['Accountability', 'Named approvers, approval levels and a permanent audit trail assign responsibility for every step.'],
                            ['Efficiency', 'Automated workflows, reference numbers and centralized documents remove paperwork and bottlenecks.'],
                            ['Compliance', 'Built-in PPA 2007 thresholds, open competitive default and Tenders Board approval gates.'],
                            ['Value for money', 'Budget tracking, three-way matching and supplier performance data drive better outcomes.'],
                        ];
                    @endphp
                    @foreach ($benefits as [$title, $desc])
                        <li class="flex gap-3">
                            <span class="mt-1 w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs shrink-0">✓</span>
                            <div>
                                <div class="text-white font-medium">{{ $title }}</div>
                                <div class="text-sm text-slate-400 mt-0.5">{{ $desc }}</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <section class="border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
            <h2 class="text-2xl font-bold text-white text-center">Governance approach</h2>
            <p class="mt-3 text-slate-400 text-center max-w-2xl mx-auto">
                ProcurePilot separates duties and enforces segregation of control, mirroring the approvals required by statute.
            </p>
            <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $gov = [
                        ['Separation of duties', 'Requesters cannot approve their own requests. Procurement officers cannot award their own tenders.'],
                        ['Threshold-based approval', 'Approval levels escalate with value — department heads, procurement, finance, and the Tenders Board.'],
                        ['Independent oversight', 'Auditors get read-only access to every record, document and approval decision for review.'],
                    ];
                @endphp
                @foreach ($gov as [$title, $desc])
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                        <h3 class="text-white font-semibold">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-slate-400">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-public-layout>
