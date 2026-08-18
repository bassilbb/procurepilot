<x-public-layout title="FAQ">
    <x-public-hero
        eyebrow="Help Center"
        title="Frequently asked questions"
        subtitle="Answers to common questions about ProcurePilot, procurement compliance and getting started." />

    <section class="max-w-3xl mx-auto px-4 sm:px-6 py-16">
        <div x-data="{ open: null }" class="space-y-4">
            @php
                $faqs = [
                    ['What is ProcurePilot?', 'ProcurePilot is an end-to-end procurement management platform for organizations that must spend funds transparently and compliantly. It covers procurement requests, approval workflows, tenders, evaluation, awards, contracts, purchase orders, goods receipt, budgets, supplier invoices and audit trails.'],
                    ['Is it compliant with the Nigerian Public Procurement Act 2007?', 'Yes. The workflow is designed around PPA 2007 principles: annual procurement plans, open competitive tendering as the default method, Tenders Board approval, documented award rationale and a complete audit trail. Thresholds and approval levels are configurable to your organization.'],
                    ['What is three-way matching?', 'Three-way matching compares the supplier invoice against the purchase order and the goods receipt. A full match confirms quantity and price agree across all three documents before an invoice is approved for payment, preventing overpayment and errors.'],
                    ['How does the trial work?', 'Every new organization gets a 14-day free trial of the Starter plan with no credit card required. You can upgrade, downgrade or cancel at any time from the Billing section.'],
                    ['Can I configure my own approval workflow?', 'Yes. Administrators can configure approval levels with role requirements and value thresholds, so requests, plans and awards route through the right approvers automatically.'],
                    ['How is the audit trail maintained?', 'Every significant action — creation, submission, approval, rejection, cancellation, deletion — is recorded with the user, timestamp, before/after values and IP address. Auditors have read-only visibility into all records.'],
                    ['What roles are supported?', 'The platform ships with Administrator, Procurement Officer, Tenders Board/Approver, Auditor/Compliance, Staff and Supplier roles, each with appropriate permissions across the system.'],
                    ['Is my data secure?', 'Access is role-based and enforced server-side on every route. Passwords are hashed, sessions are encrypted, and file uploads are restricted and stored privately.'],
                    ['Can suppliers participate directly?', 'Suppliers can be invited to tenders, submit bids and have their documents managed centrally. Supplier self-registration can be enabled for public portals.'],
                    ['What currencies and reporting are supported?', 'The system supports multiple currencies (NGN default) and includes procurement, spend, budget utilization, supplier, contract and PO reporting.'],
                ];
            @endphp
            @foreach ($faqs as $i => [$q, $a])
                <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
                    <button @click="open = open === {{ $i }} ? null : {{ $i }}" class="w-full flex items-center justify-between px-6 py-4 text-left">
                        <span class="text-white font-medium">{{ $q }}</span>
                        <svg class="w-5 h-5 text-slate-500 transition-transform" :class="open === {{ $i }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === {{ $i }}" x-cloak x-transition class="px-6 pb-5">
                        <p class="text-sm text-slate-400 leading-relaxed">{{ $a }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12 bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl p-8 text-center">
            <h3 class="text-white text-xl font-bold">Still have questions?</h3>
            <p class="mt-2 text-emerald-100 text-sm">Our team is here to help you get the most out of ProcurePilot.</p>
            <a href="{{ route('contact') }}" class="mt-5 inline-block px-6 py-2.5 bg-white text-emerald-700 font-semibold rounded-lg">Contact Support</a>
        </div>
    </section>
</x-public-layout>
