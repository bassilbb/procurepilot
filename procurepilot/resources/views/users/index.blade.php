<x-app-layout>
    <x-slot name="header">Team Members</x-slot>

    <x-page-header title="Team Management" description="Manage users and their roles within your organization" />

    @php
        $assignableRoles = auth()->user()->isSuperAdmin() ? $roles : collect($roles)->except('superadmin')->all();
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="hd-card p-6 h-fit">
            <h3 class="font-semibold text-slate-900 mb-4">Invite Team Member</h3>
            <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                @csrf
                <div>
                    <x-input-label for="name" value="Full Name" />
                    <x-text-input id="name" name="name" class="mt-1 w-full" required />
                </div>
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 w-full" required />
                </div>
                <div>
                    <x-input-label for="role" value="Role" />
                    <select id="role" name="role" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach ($assignableRoles as $key => $label)
                            <option value="{{ $key }}" @selected($key === 'procurement')>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="title" value="Job Title" />
                    <x-text-input id="title" name="title" class="mt-1 w-full" />
                </div>
                <div>
                    <x-input-label for="password" value="Temporary Password" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 w-full" required />
                </div>
                <div>
                    <x-input-label for="password_confirmation" value="Confirm Password" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 w-full" required />
                </div>
                <button class="w-full px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Add Team Member</button>
            </form>
        </div>

        <div class="lg:col-span-2 hd-card">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100 bg-slate-50">
                            <th class="py-3 px-4">User</th>
                            <th class="py-3 px-4">Role</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($users as $user)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-slate-900 text-white flex items-center justify-center text-sm font-semibold">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                        <div>
                                            <div class="font-medium text-slate-800">{{ $user->name }} @if ($user->id === auth()->id()) <span class="text-xs text-slate-400">(you)</span> @endif</div>
                                            <div class="text-xs text-slate-400">{{ $user->email }}@if ($user->title) · {{ $user->title }} @endif</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <button onclick="document.getElementById('user-role-{{ $user->id }}').showModal()" class="text-xs px-2 py-1 rounded-full border bg-slate-50 text-slate-700 hover:bg-slate-100">
                                        {{ $roles[$user->role] ?? ucfirst($user->role) }}
                                    </button>
                                    <dialog id="user-role-{{ $user->id }}" class="rounded-xl p-6 w-full max-w-xs">
                                        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                                            @csrf @method('PUT')
                                            <h3 class="font-semibold text-slate-900">Edit {{ $user->name }}</h3>
                                            <div><x-input-label for="name" value="Name" /><x-text-input id="name" name="name" class="mt-1 w-full" value="{{ $user->name }}" required /></div>
                                            <div><x-input-label for="email" value="Email" /><x-text-input id="email" name="email" type="email" class="mt-1 w-full" value="{{ $user->email }}" required /></div>
                                            <div><x-input-label for="title" value="Title" /><x-text-input id="title" name="title" class="mt-1 w-full" value="{{ $user->title }}" /></div>
                                            <div>
                                                <x-input-label for="role" value="Role" />
                                                <select id="role" name="role" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                                    @foreach ($assignableRoles as $key => $label)
                                                        <option value="{{ $key }}" @selected($user->role === $key)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="flex justify-end gap-2">
                                                <button type="button" onclick="document.getElementById('user-role-{{ $user->id }}').close()" class="px-4 py-2 text-sm text-slate-600">Cancel</button>
                                                <button class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg">Save</button>
                                            </div>
                                        </form>
                                    </dialog>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-xs px-2 py-1 rounded-full border font-medium {{ $user->is_active ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">{{ $user->is_active ? 'Active' : 'Disabled' }}</span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.toggle', $user) }}" class="inline">
                                            @csrf
                                            <button class="text-xs text-amber-600 mr-2">{{ $user->is_active ? 'Disable' : 'Enable' }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Remove this team member?')">
                                            @csrf @method('DELETE')
                                            <button class="text-xs text-red-500">Remove</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
