<x-app-layout>
    <x-slot name="header">Audit Trail</x-slot>

    <x-page-header title="Audit Log" description="Immutable record of every action — transparency & accountability" />

    <div class="hd-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100 bg-slate-50">
                        <th class="py-3 px-4">Timestamp</th>
                        <th class="py-3 px-4">User</th>
                        <th class="py-3 px-4">Action</th>
                        <th class="py-3 px-4">Resource</th>
                        <th class="py-3 px-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 text-slate-500 whitespace-nowrap">{{ $log->created_at?->format('d M Y H:i:s') }}</td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-800">{{ $log->user?->name ?? 'System' }}</div>
                                <div class="text-xs text-slate-400">{{ $log->user?->email }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-xs px-2 py-1 rounded-full border bg-slate-50 text-slate-700">{{ str_replace('_', ' ', $log->action) }}</span>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500">
                                {{ class_basename($log->auditable_type ?? '—') }}
                                @if ($log->auditable_id) #{{ $log->auditable_id }} @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-xs text-slate-400">{{ $log->ip_address ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">No audit records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6">{{ $logs->links() }}</div>
</x-app-layout>
