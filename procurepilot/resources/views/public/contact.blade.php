<x-public-layout title="Contact Us">
    <x-public-hero
        eyebrow="Support & Help Desk"
        title="We're here to help"
        subtitle="Reach the right team — support, procurement or IT — and we'll respond within one business day." />

    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div>
                <h2 class="text-xl font-bold text-white">Contact details</h2>
                <div class="mt-6 space-y-4">
                    @php
                        $contacts = [
                            ['Support / Help Desk', 'support@procurepilot.test', '+234 1 460 3700', 'Monday – Friday, 8:00 – 17:00 (WAT)'],
                            ['Procurement Department', 'procurement@procurepilot.test', '+234 1 460 3701', 'Monday – Friday, 9:00 – 16:00 (WAT)'],
                            ['IT Support', 'it@procurepilot.test', '+234 1 460 3702', '24/7 for critical incidents'],
                        ];
                    @endphp
                    @foreach ($contacts as [$name, $email, $phone, $hours])
                        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex items-start gap-4">
                            <div class="w-11 h-11 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <div class="text-white font-semibold">{{ $name }}</div>
                                <div class="mt-1 space-y-1 text-sm text-slate-400">
                                    <div><a href="mailto:{{ $email }}" class="text-emerald-400 hover:underline">{{ $email }}</a></div>
                                    <div>{{ $phone }}</div>
                                    <div class="text-xs text-slate-500">{{ $hours }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 bg-slate-900 border border-slate-800 rounded-2xl p-5">
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <div class="text-white font-semibold">Head Office</div>
                            <div class="mt-1 text-sm text-slate-400 leading-relaxed">
                                ProcurePilot Ltd<br>
                                4, Port Trade Complex, Apapa<br>
                                Lagos, Nigeria
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-bold text-white">Send us a message</h2>
                <form method="POST" action="{{ route('contact.submit') }}" class="mt-6 bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-300">Your name</label>
                        <input type="text" name="name" required class="mt-1 w-full rounded-xl border-slate-700 bg-slate-800 text-white focus:border-emerald-500 focus:ring-emerald-500" placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300">Email address</label>
                        <input type="email" name="email" required class="mt-1 w-full rounded-xl border-slate-700 bg-slate-800 text-white focus:border-emerald-500 focus:ring-emerald-500" placeholder="you@company.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300">Department</label>
                        <select name="topic" class="mt-1 w-full rounded-xl border-slate-700 bg-slate-800 text-white focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="Support">Support / Help Desk</option>
                            <option value="Procurement">Procurement</option>
                            <option value="IT">IT Support</option>
                            <option value="Sales">Sales &amp; Pricing</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300">Message</label>
                        <textarea name="message" rows="5" required class="mt-1 w-full rounded-xl border-slate-700 bg-slate-800 text-white focus:border-emerald-500 focus:ring-emerald-500" placeholder="How can we help?"></textarea>
                    </div>
                    @if (session('contact_sent'))
                        <div class="rounded-xl bg-emerald-500/10 border border-emerald-500/30 px-4 py-3 text-sm text-emerald-300">Thank you — your message has been received.</div>
                    @endif
                    <button type="submit" class="w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl">Send Message</button>
                </form>
            </div>
        </div>
    </section>
</x-public-layout>
