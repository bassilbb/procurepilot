<?php

use App\Models\Organization;

if (! function_exists('notifyUsers')) {
    function notifyUsers(iterable $users, string $title, string $body, string $type = 'info', ?string $url = null): void
    {
        foreach ($users as $user) {
            $user->notifications()->create([
                'id'   => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\SystemNotification',
                'data' => json_encode([
                    'title' => $title,
                    'body'  => $body,
                    'type'  => $type,
                    'url'   => $url,
                ]),
            ]);
        }
    }
}


if (! function_exists('currentOrganization')) {
    function currentOrganization(): ?Organization
    {
        if (app()->bound('currentOrganization')) {
            return app('currentOrganization');
        }

        $user = auth()->user();

        if ($user && $user->organization_id) {
            return $user->organization;
        }

        return null;
    }
}

if (! function_exists('formatMoney')) {
    function formatMoney(float $amount, string $currency = 'NGN'): string
    {
        $symbols = ['NGN' => '₦', 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'GHS' => 'GH₵', 'KES' => 'KSh'];

        $symbol = $symbols[$currency] ?? $currency . ' ';

        return $symbol . number_format($amount, 2);
    }
}

if (! function_exists('statusBadgeClass')) {
    function statusBadgeClass(string $status): string
    {
        $map = [
            'pending'     => 'bg-amber-100 text-amber-800 border-amber-200',
            'draft'       => 'bg-gray-100 text-gray-700 border-gray-200',
            'submitted'   => 'bg-blue-100 text-blue-800 border-blue-200',
            'published'   => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'open'        => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'active'      => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'trialing'    => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            'approved'    => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'rejected'    => 'bg-red-100 text-red-800 border-red-200',
            'closed'      => 'bg-slate-200 text-slate-700 border-slate-300',
            'under_evaluation' => 'bg-violet-100 text-violet-800 border-violet-200',
            'awarded'     => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'cancelled'   => 'bg-rose-100 text-rose-800 border-rose-200',
            'terminated'  => 'bg-rose-100 text-rose-800 border-rose-200',
            'completed'   => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'expired'     => 'bg-slate-200 text-slate-700 border-slate-300',
            'paid'        => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'failed'      => 'bg-red-100 text-red-800 border-red-200',
            'issued'      => 'bg-blue-100 text-blue-800 border-blue-200',
            'partially_received' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
            'received'    => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'suspended'   => 'bg-amber-100 text-amber-800 border-amber-200',
            'blacklisted' => 'bg-red-100 text-red-800 border-red-200',
            'success'     => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'overdue'     => 'bg-red-100 text-red-800 border-red-200',
            'evaluated'   => 'bg-violet-100 text-violet-800 border-violet-200',
            'recommended' => 'bg-blue-100 text-blue-800 border-blue-200',
            'declined'    => 'bg-red-100 text-red-800 border-red-200',
            'matched'     => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'verified'    => 'bg-cyan-100 text-cyan-800 border-cyan-200',
            'critical'    => 'bg-red-100 text-red-800 border-red-200',
        ];

        $key = strtolower($status);

        return $map[$key] ?? 'bg-gray-100 text-gray-700 border-gray-200';
    }
}
