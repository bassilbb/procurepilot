<x-app-layout>
    <x-slot name="header">New Procurement Plan</x-slot>

    <x-page-header title="Create Procurement Plan" description="Define your annual procurement plan for the fiscal year" />

    <form method="POST" action="{{ route('plans.store') }}" class="hd-card p-6 max-w-2xl">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <x-input-label for="title" value="Plan Title" />
                <x-text-input id="title" name="title" class="mt-1 w-full" required placeholder="e.g. Annual Procurement Plan FY 2025" value="{{ old('title') }}" />
            </div>
            <div>
                <x-input-label for="fiscal_year" value="Fiscal Year" />
                <select id="fiscal_year" name="fiscal_year" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    @foreach ($fiscalYears as $y)
                        <option value="{{ $y }}" @selected(old('fiscal_year', date('Y')) == $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description" rows="2" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Overview of planned procurement...">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('plans.index') }}" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Cancel</a>
            <button class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create Plan</button>
        </div>
    </form>
</x-app-layout>
