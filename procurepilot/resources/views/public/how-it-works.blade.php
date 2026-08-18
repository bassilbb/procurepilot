<x-public-layout title="How It Works">
    <x-public-hero
        eyebrow="The Procurement Lifecycle"
        title="From request to payment, step by step"
        subtitle="A governed workflow that mirrors the Public Procurement Act 2007 and international best practice." />

    <section class="max-w-4xl mx-auto px-4 sm:px-6 py-16">
        <div class="space-y-8">
            @php
                $steps = [
                    ['1', 'Request & Plan', 'Departments submit procurement requests and annual plans with item details, budget codes and justifications.', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                    ['2', 'Approval Workflow', 'Requests and plans route through configurable approval levels — department manager, procurement, finance, Tenders Board.', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['3', 'Tendering', 'Approved items move to open competitive tenders or restricted RFQs. Suppliers are invited, bids are sealed.', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['4', 'Evaluation & Award', 'Bids are scored against a weighted matrix. The best evaluated bid is recommended and approved by the Tenders Board.', 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V6a2 2 0 00-2 2h2zm5 2a5 5 0 01-10 0h10z'],
                    ['5', 'Contract', 'A contract is drawn against the award with value, terms, milestones and supporting documents.', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                    ['6', 'Purchase Order & Delivery', 'Purchase orders are issued and goods receipted against the PO, with partial delivery supported.', 'M8 7h8m-8 5h8m-4 4h4M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z'],
                    ['7', 'Invoice & Payment', 'Supplier invoices are verified and three-way matched against the PO and goods receipt before approval for payment.', 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                ];
            @endphp
            @foreach ($steps as [$num, $title, $desc, $icon])
                <div class="relative flex gap-5">
                    @if (!$loop->last)
                        <div class="absolute left-6 top-14 bottom-0 w-px bg-slate-800"></div>
                    @endif
                    <div class="relative z-10 w-12 h-12 rounded-xl bg-emerald-500 flex items-center justify-center text-white font-bold text-lg shrink-0">{{ $num }}</div>
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 flex-1">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                            </div>
                            <h3 class="text-white font-bold">{{ $title }}</h3>
                        </div>
                        <p class="mt-3 text-sm text-slate-400">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('register') }}" class="inline-block px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg">Start Your Free Trial</a>
        </div>
    </section>
</x-public-layout>
