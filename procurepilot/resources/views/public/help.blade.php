<x-public-layout title="Help & User Guide">
    <x-public-hero
        eyebrow="Documentation"
        title="User guide & system documentation"
        subtitle="A practical walkthrough of ProcurePilot's processes — for staff, procurement officers, approvers and auditors." />

    <section class="max-w-4xl mx-auto px-4 sm:px-6 py-16">
        @php
            $sections = [
                [
                    'title' => 'Getting started',
                    'items' => [
                        'Create your organization and invite team members from Settings → Team Members.',
                        'Assign roles: Administrator, Procurement Officer, Tenders Board/Approver, Auditor/Compliance, Staff, Supplier.',
                        'Configure departments, budget lines and approval levels before submitting your first request.',
                        'Use the 14-day trial to explore every module risk-free.',
                    ],
                ],
                [
                    'title' => 'Procurement requests',
                    'items' => [
                        'Staff submit requests with items, estimated costs, budget code and required date.',
                        'Requests route through your configured approval levels for sign-off.',
                        'Approved requests flow into procurement planning and tendering.',
                    ],
                ],
                [
                    'title' => 'The tendering process',
                    'items' => [
                        'Create a tender, add items, define evaluation criteria and invite or open to suppliers.',
                        'Publish to open the tender and set a closing date/time.',
                        'Collect sealed bids, then begin evaluation when the tender closes.',
                        'Score bids against the weighted criteria; the system ranks bidders automatically.',
                        'Recommend the best evaluated bid for Tenders Board approval.',
                    ],
                ],
                [
                    'title' => 'Awards, contracts and purchase orders',
                    'items' => [
                        'Approve the award recommendation to create a contract.',
                        'Add milestones, payment terms and supporting documents to the contract.',
                        'Create and issue purchase orders against the contract or directly.',
                        'Record goods receipts against each PO line; partial deliveries update status automatically.',
                    ],
                ],
                [
                    'title' => 'Budgets and invoices',
                    'items' => [
                        'Set annual budgets per department with committed and spent tracking.',
                        'Register supplier invoices and link them to the purchase order.',
                        'Run three-way matching to verify the invoice against the PO and goods receipt before approval.',
                    ],
                ],
                [
                    'title' => 'Approvals and audits',
                    'items' => [
                        'Approvers see pending items on their dashboard and act with a single click.',
                        'Every action is recorded in the Audit Trail with user, time and before/after values.',
                        'Auditors can review all records read-only from the Compliance page.',
                    ],
                ],
                [
                    'title' => 'Troubleshooting',
                    'items' => [
                        'Forgot your password? Use "Forgot password" on the sign-in page.',
                        'Can\'t see a module? Your role may not have permission — contact an administrator.',
                        'Trial expired? Choose a plan from Billing & Subscriptions to continue.',
                        'Contact IT Support for login, access or technical issues.',
                    ],
                ],
            ];
        @endphp

        <div class="space-y-6">
            @foreach ($sections as $section)
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                    <h2 class="text-white font-bold flex items-center gap-2">
                        <span class="w-6 h-6 rounded-md bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">✓</span>
                        {{ $section['title'] }}
                    </h2>
                    <ul class="mt-4 space-y-2">
                        @foreach ($section['items'] as $item)
                            <li class="flex gap-3 text-sm text-slate-400">
                                <span class="text-slate-600 mt-1">•</span>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <div class="mt-10 text-center">
            <p class="text-slate-400 text-sm">Need more help? Contact our support team.</p>
            <a href="{{ route('contact') }}" class="mt-3 inline-block px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg">Contact Support</a>
        </div>
    </section>
</x-public-layout>
