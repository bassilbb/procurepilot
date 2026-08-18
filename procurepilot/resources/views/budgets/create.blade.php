<x-app-layout>
    <x-slot name="header">New Budget</x-slot>

    <x-page-header title="Create Budget" description="Set up an allocation for a department or category" />

    <form method="POST" action="{{ route('budgets.store') }}">
        @csrf

        <div class="hd-card p-6 max-w-3xl">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">Budget details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="name" :value="__('Budget name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" placeholder="e.g. IT Equipment 2026" required autofocus />
                </div>
                <div>
                    <x-input-label for="fiscal_year" :value="__('Fiscal year')" />
                    <x-text-input id="fiscal_year" class="block mt-1 w-full" type="text" name="fiscal_year" :value="old('fiscal_year', date('Y'))" required />
                </div>
                <div>
                    <x-input-label for="department_id" :value="__('Department (optional)')" />
                    <select id="department_id" name="department_id" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">— None —</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="category" :value="__('Category')" />
                    <select id="category" name="category" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="operating" @selected(old('category') === 'operating')>Operating</option>
                        <option value="capital" @selected(old('category') === 'capital')>Capital</option>
                        <option value="projects" @selected(old('category') === 'projects')>Projects</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="allocated_amount" :value="__('Allocated amount (₦)')" />
                    <x-text-input id="allocated_amount" class="block mt-1 w-full" type="number" name="allocated_amount" :value="old('allocated_amount')" step="0.01" min="0" required />
                </div>
                <div>
                    <x-input-label for="currency" :value="__('Currency')" />
                    <x-text-input id="currency" class="block mt-1 w-full" type="text" name="currency" :value="old('currency', 'NGN')" />
                </div>
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="active" @selected(old('status') === 'active')>Active</option>
                        <option value="draft" @selected(old('status') === 'draft')>Draft</option>
                        <option value="closed" @selected(old('status') === 'closed')>Closed</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="notes" :value="__('Notes')" />
                    <textarea id="notes" name="notes" rows="3" class="mt-1 w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm" placeholder="Purpose, caveats, etc.">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-6 max-w-3xl">
            <a href="{{ route('budgets.index') }}" class="px-5 py-2.5 text-sm text-slate-600 hover:text-slate-800">Cancel</a>
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Create Budget</button>
        </div>
    </form>
</x-app-layout>
