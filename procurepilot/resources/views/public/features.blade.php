<x-public-layout title="Features">
    <x-public-hero
        eyebrow="Capabilities"
        title="Everything procurement needs, in one system"
        subtitle="From annual planning to payment — competitive tendering, supplier management, budgets, contracts and full audit trail." />

    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $features = [
                    ['Procurement Requests', 'Departmental staff submit requests with justification, budget code and required date, routed through approval levels.', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                    ['Annual Procurement Plans', 'Fiscal-year plans with items, estimated costs and procurement method, submitted for Tenders Board approval.', 'M8 7V3m0 0l-2 2m2-2l2 2M8 7v4m8-4V3m0 0l-2 2m2-2l2 2m-2 4v4m-4-1v4m4 3a2 2 0 100-4 2 2 0 000 4zm-8 0a2 2 0 100-4 2 2 0 000 4z'],
                    ['Tenders & RFQs', 'Publish open competitive tenders, invite suppliers, enforce deadlines and collect sealed bids.', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['Evaluation & Awards', 'Weighted scoring matrix against evaluation criteria, technical and commercial scoring, award recommendation and Tenders Board approval.', 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V6a2 2 0 00-2 2h2zm5 2a5 5 0 01-10 0h10z'],
                    ['Supplier Management', 'Supplier registration, document management, approval workflow, ratings, suspension and blacklisting.', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['Contracts & Milestones', 'Contract register with milestones, documents, activations, completions and expiry alerts.', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                    ['Purchase Orders', 'Create and issue POs against contracts, approve, track partial receipt and cancellation.', 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
                    ['Budget Management', 'Set budgets, track commitments and spend, monitor utilization and remaining balance per line.', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['Invoices & 3-Way Matching', 'Register supplier invoices, verify against purchase order and goods receipt for full three-way matching.', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['Reports & Insights', 'Procurement summaries, spend by category and supplier, budget utilization, contract and PO reports.', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ['Audit Trail', 'Every create, approve, reject and delete is logged with user, timestamp, before/after values and IP.', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                    ['Role-Based Security', 'Granular roles — admin, procurement, approver, auditor, staff, supplier — with per-route authorization.', 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                ];
            @endphp
            @foreach ($features as [$title, $desc, $icon])
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                    <div class="w-11 h-11 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <h3 class="text-white font-semibold">{{ $title }}</h3>
                    <p class="mt-2 text-sm text-slate-400">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>
</x-public-layout>
