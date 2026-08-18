<x-app-layout>
    <x-slot name="header">Categories</x-slot>

    <x-page-header title="Procurement Categories" description="Goods, services and works classifications for procurement" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="hd-card p-6 h-fit">
            <h3 class="font-semibold text-slate-900 mb-4">Add Category</h3>
            <form method="POST" action="{{ route('categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <x-input-label for="name" value="Name" />
                    <x-text-input id="name" name="name" class="mt-1 w-full" required placeholder="e.g. Marine Equipment" />
                </div>
                <div>
                    <x-input-label for="code" value="Code" />
                    <x-text-input id="code" name="code" class="mt-1 w-full" placeholder="ME-01" />
                </div>
                <div>
                    <x-input-label for="type" value="Type" />
                    <select id="type" name="type" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="goods">Goods</option>
                        <option value="services">Services</option>
                        <option value="works">Works</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="description" value="Description" />
                    <textarea id="description" name="description" rows="2" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </div>
                <button class="w-full px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Add Category</button>
            </form>
        </div>

        <div class="lg:col-span-2 hd-card p-6">
            <h3 class="font-semibold text-slate-900 mb-4">Categories ({{ $categories->count() }})</h3>
            @if ($categories->isEmpty())
                <p class="text-sm text-slate-500">No categories yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase text-slate-400 border-b border-slate-100">
                                <th class="py-3 pr-4">Name</th>
                                <th class="py-3 pr-4">Code</th>
                                <th class="py-3 pr-4">Type</th>
                                <th class="py-3 pr-4">Description</th>
                                <th class="py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($categories as $category)
                                <tr>
                                    <td class="py-3 pr-4 font-medium text-slate-800">{{ $category->name }}</td>
                                    <td class="py-3 pr-4 text-slate-500">{{ $category->code ?? '—' }}</td>
                                    <td class="py-3 pr-4">
                                        <span class="text-xs px-2 py-0.5 rounded-full border {{ $category->type === 'works' ? 'bg-amber-100 text-amber-700 border-amber-200' : ($category->type === 'services' ? 'bg-sky-100 text-sky-700 border-sky-200' : 'bg-emerald-100 text-emerald-700 border-emerald-200') }}">{{ ucfirst($category->type) }}</span>
                                    </td>
                                    <td class="py-3 pr-4 text-slate-500">{{ $category->description }}</td>
                                    <td class="py-3 text-right">
                                        <button onclick="document.getElementById('edit-cat-{{ $category->id }}').showModal()" class="text-xs text-slate-500 hover:text-slate-700 mr-2">Edit</button>
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Remove this category?')">
                                            @csrf @method('DELETE')
                                            <button class="text-xs text-red-500 hover:text-red-700">Delete</button>
                                        </form>

                                        <dialog id="edit-cat-{{ $category->id }}" class="rounded-xl p-6 w-full max-w-md">
                                            <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-4">
                                                @csrf @method('PUT')
                                                <h3 class="font-semibold text-slate-900">Edit Category</h3>
                                                <div><x-input-label for="name" value="Name" /><x-text-input id="name" name="name" class="mt-1 w-full" value="{{ $category->name }}" required /></div>
                                                <div><x-input-label for="code" value="Code" /><x-text-input id="code" name="code" class="mt-1 w-full" value="{{ $category->code }}" /></div>
                                                <div>
                                                    <x-input-label for="type" value="Type" />
                                                    <select id="type" name="type" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                                        @foreach (['goods', 'services', 'works'] as $t)
                                                            <option value="{{ $t }}" @selected($category->type === $t)>{{ ucfirst($t) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div><x-input-label for="description" value="Description" /><textarea id="description" name="description" rows="2" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">{{ $category->description }}</textarea></div>
                                                <div class="flex justify-end gap-2">
                                                    <button type="button" onclick="document.getElementById('edit-cat-{{ $category->id }}').close()" class="px-4 py-2 text-sm text-slate-600">Cancel</button>
                                                    <button class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg">Save</button>
                                                </div>
                                            </form>
                                        </dialog>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
