<x-app-layout>
    <x-slot name="header">Role & Access Management</x-slot>

    <x-page-header title="Role & Access Management" description="Assign which modules each role can view, create, edit, delete or approve (Super Admin only)" />

    <div class="hd-card overflow-hidden">
        <form method="POST" action="{{ route('access-control.update') }}" x-data="{ selected: {} }">
            @csrf @method('PUT')

            <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <p class="text-sm text-slate-600">
                        <span class="font-semibold text-slate-900">Super Admin</span> and <span class="font-semibold text-slate-900">Administrator</span> roles always have full access to every module.
                    </p>
                    <p class="text-xs text-slate-400 mt-1">Changes apply immediately to all users holding the role.</p>
                </div>
                <button class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 shrink-0">Save Access</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100 bg-slate-50">
                            <th class="py-3 px-4 min-w-[220px] sticky left-0 bg-slate-50">Module</th>
                            @foreach ($roles as $role)
                                <th class="py-3 px-4 text-center">
                                    <div class="font-semibold text-slate-700">{{ ucfirst($role) }}</div>
                                    <div class="flex justify-center gap-1 mt-1">
                                        <span class="text-[10px] text-slate-400 font-normal">V</span>
                                        <span class="text-[10px] text-slate-400 font-normal">C</span>
                                        <span class="text-[10px] text-slate-400 font-normal">E</span>
                                        <span class="text-[10px] text-slate-400 font-normal">D</span>
                                        <span class="text-[10px] text-slate-400 font-normal">A</span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($modules as $moduleKey => $moduleLabel)
                            <tr class="border-b border-slate-50 hover:bg-slate-50/60">
                                <td class="py-3 px-4 sticky left-0 bg-white font-medium text-slate-800">{{ $moduleLabel }}</td>
                                @foreach ($roles as $role)
                                    @php
                                        $perm = $permissions[$role . '.' . $moduleKey] ?? null;
                                    @endphp
                                    <td class="py-3 px-4">
                                        <div class="flex items-center justify-center gap-1">
                                            @foreach (['view' => 'view', 'create' => 'create', 'edit' => 'edit', 'delete' => 'delete', 'approve' => 'approve'] as $action => $label)
                                                <label class="relative inline-flex items-center cursor-pointer" title="{{ ucfirst($action) }}">
                                                    <input type="checkbox"
                                                           name="permissions[{{ $role }}][{{ $moduleKey }}][]"
                                                           value="{{ $action }}"
                                                           class="w-3.5 h-3.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                                           {{ $perm && $perm->{'can_' . $action} ? 'checked' : '' }}>
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 flex justify-end">
                <button class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Save Access</button>
            </div>
        </form>
    </div>
</x-app-layout>
