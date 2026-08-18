<x-app-layout>
    <x-slot name="header">Workflow Configuration</x-slot>

    <div class="relative mb-8 rounded-2xl overflow-hidden border border-slate-200"
         style="background: linear-gradient(120deg, #f0fdf4 0%, #ecfdf5 35%, #eef2ff 100%); box-shadow: 0 1px 0 rgba(255,255,255,.9) inset, 0 8px 24px -8px rgba(15,23,42,.12);">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(circle at 20% 20%, #059669 0, transparent 45%), radial-gradient(circle at 80% 30%, #6366f1 0, transparent 40%), radial-gradient(circle at 60% 90%, #10b981 0, transparent 45%);"></div>
        <div class="relative px-6 py-6 sm:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5 mb-1">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 6px 14px -4px rgba(16,185,129,.6), inset 0 1px 0 rgba(255,255,255,.35);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                    </div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Workflow Configuration</h2>
                </div>
                <p class="text-sm text-slate-600 pl-[46px]">Departments and approval levels · PPA 2007 aligned</p>
            </div>
            <div class="flex items-center gap-2 pl-[46px] sm:pl-0">
                <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-white/80 border border-slate-200 text-slate-600 shadow-sm">{{ $departments->count() }} Departments</span>
                <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-white/80 border border-slate-200 text-slate-600 shadow-sm">{{ $levels->count() }} Approval Levels</span>
            </div>
        </div>
    </div>

    <div x-data="workflowModals()">
        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-lg">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="workflow-card">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="workflow-card-title"><span class="badge-dot"></span> Departments</h3>
                    @if (auth()->user()->isAdmin())
                        <button type="button" @click="openDepartmentForm" class="btn-3d btn-3d-add">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            Add
                        </button>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm wf-table">
                        <thead>
                            <tr class="text-left uppercase text-slate-400 border-b border-slate-200/70">
                                <th class="py-3 px-6">Name</th>
                                <th class="py-3 px-4">Code</th>
                                <th class="py-3 px-4">Manager</th>
                                <th class="py-3 px-4 text-center">Members</th>
                                <th class="py-3 px-4">Status</th>
                                @if (auth()->user()->isAdmin())<th class="py-3 px-4 text-right">Actions</th>@endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($departments as $dept)
                                <tr class="group">
                                    <td class="py-3 px-6 font-semibold text-slate-800">
                                        <span class="inline-flex items-center gap-2">
                                            <span class="w-7 h-7 rounded-lg flex items-center justify-center text-[11px] font-bold text-emerald-700 shrink-0" style="background: linear-gradient(135deg,#ecfdf5,#d1fae5); border:1px solid rgba(16,185,129,.25);">{{ mb_strtoupper(mb_substr($dept->name, 0, 2)) }}</span>
                                            {{ $dept->name }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 font-mono text-xs text-slate-500">{{ $dept->code ?? '—' }}</td>
                                    <td class="py-3 px-4 text-slate-600">{{ $dept->manager?->name ?? '—' }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[1.75rem] px-1.5 py-0.5 rounded-full text-xs font-bold text-slate-700" style="background:linear-gradient(135deg,#f8fafc,#e2e8f0); border:1px solid rgba(148,163,184,.3);">{{ $dept->users->count() }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="text-xs px-2.5 py-1 rounded-full border font-semibold {{ $dept->is_active ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">{{ $dept->is_active ? 'Active' : 'Inactive' }}</span>
                                    </td>
                                    @if (auth()->user()->isAdmin())
                                        <td class="py-3 px-4">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button type="button" @click="openDeptEdit({{ $dept->id }})" class="btn-3d btn-3d-edit" title="Edit department">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Edit
                                                </button>
                                                @if ($dept->users->count() === 0)
                                                    <button type="button" @click="openDelete('{{ route('workflow.departments.destroy', $dept) }}', 'Remove this department?')" class="btn-3d btn-3d-danger" title="Remove department">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        Remove
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-12 text-center text-slate-400">No departments.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="workflow-card">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="workflow-card-title"><span class="badge-dot" style="background:linear-gradient(135deg,#6366f1,#4f46e5); box-shadow:0 0 0 3px rgba(99,102,241,.18), 0 2px 6px rgba(99,102,241,.4);"></span> Approval levels</h3>
                    @if (auth()->user()->isAdmin())
                        <button type="button" @click="openLevelForm" class="btn-3d btn-3d-add">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            Add
                        </button>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm wf-table">
                        <thead>
                            <tr class="text-left uppercase text-slate-400 border-b border-slate-200/70">
                                <th class="py-3 px-6">Seq</th>
                                <th class="py-3 px-4">Level</th>
                                <th class="py-3 px-4">Role</th>
                                <th class="py-3 px-4">Amount Range</th>
                                <th class="py-3 px-4">Status</th>
                                @if (auth()->user()->isAdmin())<th class="py-3 px-4 text-right">Actions</th>@endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($levels as $level)
                                <tr class="group">
                                    <td class="py-3 px-6">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold text-indigo-700" style="background:linear-gradient(135deg,#eef2ff,#e0e7ff); border:1px solid rgba(99,102,241,.25); box-shadow:0 2px 6px -2px rgba(99,102,241,.35);">{{ $level->sequence }}</span>
                                    </td>
                                    <td class="py-3 px-4 font-semibold text-slate-800">{{ $level->name }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs px-2.5 py-1 rounded-full border font-semibold inline-flex items-center gap-1.5 {{ match($level->role) { 'admin' => 'bg-violet-100 text-violet-800 border-violet-200', 'approver' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'procurement' => 'bg-sky-100 text-sky-800 border-sky-200', 'auditor' => 'bg-amber-100 text-amber-800 border-amber-200', default => 'bg-slate-100 text-slate-600 border-slate-200' } }}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                {{ $level->roleLabel() }}
                                            </span>
                                        </div>
                                        @php $approvers = $level->approverUsers(); @endphp
                                        @if ($approvers->isNotEmpty())
                                            <div class="mt-1.5 flex flex-wrap gap-1">
                                                @foreach ($approvers as $approver)
                                                    <span class="text-[11px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 border border-slate-200/60">{{ $approver->name }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">
                                        <span class="font-medium text-slate-700">₦ {{ number_format($level->min_amount, 0) }}</span>
                                        <span class="text-slate-400">—</span>
                                        @if ($level->max_amount)
                                            <span class="font-medium text-slate-700">₦ {{ number_format($level->max_amount, 0) }}</span>
                                        @else
                                            <span class="text-slate-400">Unlimited</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4"><span class="text-xs px-2.5 py-1 rounded-full border font-semibold {{ $level->is_active ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">{{ $level->is_active ? 'Active' : 'Inactive' }}</span></td>
                                    @if (auth()->user()->isAdmin())
                                        <td class="py-3 px-4">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button type="button" @click="openLevelEdit({{ $level->id }})" class="btn-3d btn-3d-edit" title="Edit level">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Edit
                                                </button>
                                                @if ($level->records->count() === 0)
                                                    <button type="button" @click="openDelete('{{ route('workflow.levels.destroy', $level) }}', 'Remove this approval level?')" class="btn-3d btn-3d-danger" title="Remove level">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        Remove
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-12 text-center text-slate-400">No approval levels.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="workflow-card">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="workflow-card-title"><span class="badge-dot" style="background:linear-gradient(135deg,#f59e0b,#d97706); box-shadow:0 0 0 3px rgba(245,158,11,.18), 0 2px 6px rgba(245,158,11,.4);"></span> Pending approvals</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($pending as $record)
                        <div class="px-6 py-3.5 flex items-center justify-between text-sm">
                            <div>
                                <div class="font-medium text-slate-800">{{ $record->approvable?->title ?? '#' . $record->approvable_id }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">via {{ $record->level?->name }} · {{ $record->created_at->diffForHumans() }}</div>
                            </div>
                            <span class="pending-pill text-xs px-2.5 py-1 rounded-full font-semibold">Pending</span>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-slate-400">No pending approvals.</div>
                    @endforelse
                </div>
            </div>

            <div class="workflow-card">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="workflow-card-title"><span class="badge-dot" style="background:linear-gradient(135deg,#64748b,#475569); box-shadow:0 0 0 3px rgba(100,116,139,.18), 0 2px 6px rgba(100,116,139,.4);"></span> Approval history</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm wf-table">
                        <thead>
                            <tr class="text-left uppercase text-slate-400 border-b border-slate-200/70">
                                <th class="py-3 px-6">Item</th>
                                <th class="py-3 px-4">Level</th>
                                <th class="py-3 px-4">Approver</th>
                                <th class="py-3 px-4">Result</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($records as $record)
                                <tr>
                                    <td class="py-3 px-6 text-slate-700">{{ class_basename($record->approvable_type) }} #{{ $record->approvable_id }}</td>
                                    <td class="py-3 px-4 text-slate-600">{{ $record->level?->name ?? '—' }}</td>
                                    <td class="py-3 px-4 text-slate-600">{{ $record->approver?->name ?? '—' }}</td>
                                    <td class="py-3 px-4"><span class="text-xs px-2.5 py-1 rounded-full border font-semibold {{ statusBadgeClass($record->status) }}">{{ ucfirst($record->status) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-10 text-center text-slate-400">No approval records.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-6">{{ $records->links() }}</div>

        @if (auth()->user()->isAdmin())
            {{-- Add department modal --}}
            <div x-show="showDepartmentForm" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto">
                <div class="min-h-full flex items-center justify-center p-4 sm:p-6">
                    <div class="modal-backdrop" @click="closeDepartmentForm"></div>
                    <div class="modal-panel relative w-full max-w-lg my-8"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-6 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-6 scale-95">
                        <form method="POST" action="{{ route('workflow.departments.store') }}">
                            @csrf
                            <div class="modal-header">
                                <div class="modal-header-icon">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <div>
                                    <h3 class="modal-title">Add department</h3>
                                    <p class="modal-subtitle">Register a new department in the organisation</p>
                                </div>
                                <button type="button" @click="closeDepartmentForm" class="modal-close" aria-label="Close">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="modal-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div><x-input-label :value="__('Name')" /><x-text-input class="block mt-1 w-full" type="text" name="name" required /></div>
                                <div><x-input-label :value="__('Code')" /><x-text-input class="block mt-1 w-full" type="text" name="code" /></div>
                                <div class="sm:col-span-2">
                                    <x-input-label :value="__('Manager')" />
                                    <select name="manager_id" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                        <option value="">— None —</option>
                                        @foreach ($users as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }} ({{ ucfirst($u->role) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="sm:col-span-2"><x-input-label :value="__('Description')" /><textarea name="description" rows="2" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"></textarea></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" @click="closeDepartmentForm" class="btn-3d btn-3d-edit">Cancel</button>
                                <button class="btn-3d btn-3d-add">Save department</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Edit department modals --}}
            @foreach ($departments as $dept)
                <div x-show="showDeptEdit && deptEditId === {{ $dept->id }}" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="min-h-full flex items-center justify-center p-4 sm:p-6">
                        <div class="modal-backdrop" @click="closeDeptEdit"></div>
                        <div class="modal-panel relative w-full max-w-lg my-8"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-6 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-6 scale-95">
                            <form method="POST" action="{{ route('workflow.departments.update', $dept) }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <div class="modal-header-icon">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="modal-title">Edit department</h3>
                                        <p class="modal-subtitle">Update details for {{ $dept->name }}</p>
                                    </div>
                                    <button type="button" @click="closeDeptEdit" class="modal-close" aria-label="Close">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <div class="modal-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div><x-input-label :value="__('Name')" /><x-text-input class="block mt-1 w-full" type="text" name="name" :value="$dept->name" required /></div>
                                    <div><x-input-label :value="__('Code')" /><x-text-input class="block mt-1 w-full" type="text" name="code" :value="$dept->code" /></div>
                                    <div class="sm:col-span-2">
                                        <x-input-label :value="__('Manager')" />
                                        <select name="manager_id" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                            <option value="">— None —</option>
                                            @foreach ($users as $u)
                                                <option value="{{ $u->id }}" @selected($dept->manager_id === $u->id)>{{ $u->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="sm:col-span-2"><x-input-label :value="__('Description')" /><textarea name="description" rows="2" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">{{ $dept->description }}</textarea></div>
                                    <div class="sm:col-span-2"><label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked($dept->is_active) class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"> Active</label></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" @click="closeDeptEdit" class="btn-3d btn-3d-edit">Cancel</button>
                                    <button class="btn-3d btn-3d-add">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Add approval level modal --}}
            <div x-show="showLevelForm" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto">
                <div class="min-h-full flex items-center justify-center p-4 sm:p-6">
                    <div class="modal-backdrop" @click="closeLevelForm"></div>
                    <div class="modal-panel relative w-full max-w-lg my-8"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-6 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-6 scale-95">
                        <form method="POST" action="{{ route('workflow.levels.store') }}">
                            @csrf
                            <div class="modal-header">
                                <div class="modal-header-icon" style="background: linear-gradient(135deg, #6366f1, #4f46e5); box-shadow: 0 8px 18px -6px rgba(99,102,241,.6), inset 0 1px 0 rgba(255,255,255,.35);">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h16M4 12h16M4 19h16"/></svg>
                                </div>
                                <div>
                                    <h3 class="modal-title">Add approval level</h3>
                                    <p class="modal-subtitle">Define a step in the approval chain</p>
                                </div>
                                <button type="button" @click="closeLevelForm" class="modal-close" aria-label="Close">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="modal-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div><x-input-label :value="__('Level name')" /><x-text-input class="block mt-1 w-full" type="text" name="name" placeholder="e.g. Tenders Board" required /></div>
                                <div><x-input-label :value="__('Sequence')" /><x-text-input class="block mt-1 w-full" type="number" name="sequence" value="1" min="1" required /></div>
                                <div>
                                    <x-input-label :value="__('Required role')" />
                                    <select name="role" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                        @foreach (['approver', 'admin', 'procurement', 'auditor'] as $role)
                                            <option value="{{ $role }}">{{ match($role) { 'approver' => 'Approver', 'admin' => 'Administrator', 'procurement' => 'Procurement Officer', 'auditor' => 'Auditor / Compliance' } }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div><x-input-label :value="__('Min amount (₦)')" /><x-text-input class="block mt-1 w-full" type="number" name="min_amount" value="0" step="0.01" min="0" required /></div>
                                <div class="sm:col-span-2"><x-input-label :value="__('Max amount (₦, leave blank for unlimited)')" /><x-text-input class="block mt-1 w-full" type="number" name="max_amount" step="0.01" min="0" /></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" @click="closeLevelForm" class="btn-3d btn-3d-edit">Cancel</button>
                                <button class="btn-3d btn-3d-add">Save level</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Edit approval level modals --}}
            @foreach ($levels as $level)
                <div x-show="showLevelEdit && levelEditId === {{ $level->id }}" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="min-h-full flex items-center justify-center p-4 sm:p-6">
                        <div class="modal-backdrop" @click="closeLevelEdit"></div>
                        <div class="modal-panel relative w-full max-w-lg my-8"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-6 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-6 scale-95">
                            <form method="POST" action="{{ route('workflow.levels.update', $level) }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <div class="modal-header-icon" style="background: linear-gradient(135deg, #6366f1, #4f46e5); box-shadow: 0 8px 18px -6px rgba(99,102,241,.6), inset 0 1px 0 rgba(255,255,255,.35);">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="modal-title">Edit approval level</h3>
                                        <p class="modal-subtitle">Update {{ $level->name }}</p>
                                    </div>
                                    <button type="button" @click="closeLevelEdit" class="modal-close" aria-label="Close">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <div class="modal-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div><x-input-label :value="__('Level name')" /><x-text-input class="block mt-1 w-full" type="text" name="name" :value="$level->name" required /></div>
                                    <div><x-input-label :value="__('Sequence')" /><x-text-input class="block mt-1 w-full" type="number" name="sequence" :value="$level->sequence" min="1" required /></div>
                                    <div>
                                        <x-input-label :value="__('Required role')" />
                                        <select name="role" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                            @foreach (['approver', 'admin', 'procurement', 'auditor'] as $role)
                                                <option value="{{ $role }}" @selected($level->role === $role)>{{ match($role) { 'approver' => 'Approver', 'admin' => 'Administrator', 'procurement' => 'Procurement Officer', 'auditor' => 'Auditor / Compliance' } }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div><x-input-label :value="__('Min amount (₦)')" /><x-text-input class="block mt-1 w-full" type="number" name="min_amount" :value="$level->min_amount" step="0.01" min="0" required /></div>
                                    <div class="sm:col-span-2"><x-input-label :value="__('Max amount (₦)')" /><x-text-input class="block mt-1 w-full" type="number" name="max_amount" :value="$level->max_amount" step="0.01" min="0" /></div>
                                    <div class="sm:col-span-2"><label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked($level->is_active) class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"> Active</label></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" @click="closeLevelEdit" class="btn-3d btn-3d-edit">Cancel</button>
                                    <button class="btn-3d btn-3d-add">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Delete confirmation modal --}}
            <div x-show="showDelete" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto">
                <div class="min-h-full flex items-center justify-center p-4 sm:p-6">
                    <div class="modal-backdrop" @click="closeDelete"></div>
                    <div class="modal-panel relative w-full max-w-md my-8"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-6 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-6 scale-95">
                        <form method="POST" :action="deleteAction">
                            @csrf
                            @method('DELETE')
                            <div class="modal-header">
                                <div class="modal-header-icon" style="background: linear-gradient(135deg, #f43f5e, #dc2626); box-shadow: 0 8px 18px -6px rgba(220,38,38,.6), inset 0 1px 0 rgba(255,255,255,.35);">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </div>
                                <div>
                                    <h3 class="modal-title">Confirm removal</h3>
                                    <p class="modal-subtitle">This action cannot be undone</p>
                                </div>
                                <button type="button" @click="closeDelete" class="modal-close" aria-label="Close">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="flex items-start gap-3 p-4 rounded-xl" style="background: linear-gradient(135deg, #fef2f2 0%, #fff7ed 100%); border: 1px solid rgba(220,38,38,.18);">
                                    <div class="flex-1">
                                        <div class="text-sm font-bold text-slate-800">Are you sure you want to remove <span x-text="deleteItem" class="text-red-600"></span></div>
                                        <p class="text-xs text-slate-500 mt-1">The item will be permanently deleted and cannot be recovered.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" @click="closeDelete" class="btn-3d btn-3d-edit">Cancel</button>
                                <button class="btn-3d btn-3d-danger">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Remove
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function workflowModals() {
            return {
                showDepartmentForm: false,
                showLevelForm: false,
                showDeptEdit: false,
                deptEditId: null,
                showLevelEdit: false,
                levelEditId: null,
                showDelete: false,
                deleteAction: null,
                deleteLabel: null,
                deleteItem: null,
                openDelete(action, item) {
                    this.deleteAction = action;
                    this.deleteItem = item;
                    this.deleteLabel = item + '?';
                    this.showDelete = true;
                },
                closeDelete() {
                    this.showDelete = false;
                },
                openDepartmentForm() {
                    this.showDepartmentForm = true;
                },
                closeDepartmentForm() {
                    this.showDepartmentForm = false;
                },
                openLevelForm() {
                    this.showLevelForm = true;
                },
                closeLevelForm() {
                    this.showLevelForm = false;
                },
                openDeptEdit(id) {
                    this.deptEditId = id;
                    this.showDeptEdit = true;
                },
                closeDeptEdit() {
                    this.showDeptEdit = false;
                },
                openLevelEdit(id) {
                    this.levelEditId = id;
                    this.showLevelEdit = true;
                },
                closeLevelEdit() {
                    this.showLevelEdit = false;
                },
            };
        }
    </script>
</x-app-layout>
