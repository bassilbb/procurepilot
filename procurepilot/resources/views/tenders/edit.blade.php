<x-app-layout>
    <x-slot name="header">Edit Tender</x-slot>

    <x-page-header :title="$tender->reference" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Tender Details</h3>
                <form method="POST" action="{{ route('tenders.update', $tender) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf @method('PUT')
                    <div class="md:col-span-2">
                        <x-input-label for="title" value="Title" />
                        <x-text-input id="title" name="title" class="mt-1 w-full" value="{{ $tender->title }}" required />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="3" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">{{ $tender->description }}</textarea>
                    </div>
                    <div>
                        <x-input-label for="category_id" value="Category" />
                        <select id="category_id" name="category_id" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Select</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected($tender->category_id == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="budget" value="Budget" />
                        <x-text-input id="budget" name="budget" type="number" step="0.01" min="0" class="mt-1 w-full" value="{{ $tender->budget }}" />
                    </div>
                    <div>
                        <x-input-label for="type" value="Type" />
                        <select id="type" name="type" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="open" @selected($tender->type === 'open')>Open</option>
                            <option value="restricted" @selected($tender->type === 'restricted')>Restricted</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="evaluation_method" value="Evaluation Method" />
                        <select id="evaluation_method" name="evaluation_method" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach (['weighted_score', 'lowest_price', 'quality_cost'] as $m)
                                <option value="{{ $m }}" @selected($tender->evaluation_method === $m)>{{ ucwords(str_replace('_', ' ', $m)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="closing_at" value="Submission Deadline" />
                        <input id="closing_at" name="closing_at" type="datetime-local" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" value="{{ $tender->closing_at?->format('Y-m-d\TH:i') }}" />
                    </div>
                    <div>
                        <x-input-label for="opening_at" value="Bid Opening" />
                        <input id="opening_at" name="opening_at" type="datetime-local" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" value="{{ $tender->opening_at?->format('Y-m-d\TH:i') }}" />
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <button class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Save Tender</button>
                    </div>
                </form>
            </div>

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Add Line Item</h3>
                <form method="POST" action="{{ route('tenders.items.store', $tender) }}" class="grid grid-cols-12 gap-2">
                    @csrf
                    <div class="col-span-5"><input name="description" placeholder="Description" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" required /></div>
                    <div class="col-span-2"><input name="quantity" type="number" min="0.01" step="0.01" placeholder="Qty" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" required /></div>
                    <div class="col-span-2"><input name="unit" placeholder="Unit" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                    <div class="col-span-2"><input name="estimated_unit_price" type="number" min="0" step="0.01" placeholder="Unit price" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                    <div class="col-span-1"><button class="w-full px-2 py-2 bg-emerald-600 text-white text-xs rounded-lg">Add</button></div>
                </form>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($tender->items as $item)
                                <tr>
                                    <td class="py-2 pr-2 text-slate-800">{{ $item->description }}</td>
                                    <td class="py-2 pr-2 text-slate-500">{{ $item->quantity }} {{ $item->unit }}</td>
                                    <td class="py-2 pr-2 text-slate-500">{{ $item->estimated_unit_price ? number_format($item->estimated_unit_price, 2) : '—' }}</td>
                                    <td class="py-2 text-right">
                                        <form method="POST" action="{{ route('tenders.items.destroy', $item) }}" onsubmit="return confirm('Remove?')">
                                            @csrf @method('DELETE')
                                            <button class="text-xs text-red-500">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="hd-card p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Add Evaluation Criterion</h3>
                <form method="POST" action="{{ route('tenders.criteria.store', $tender) }}" class="grid grid-cols-12 gap-2">
                    @csrf
                    <div class="col-span-5"><input name="name" placeholder="Criterion (e.g. Technical Capability)" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" required /></div>
                    <div class="col-span-2"><input name="weight" type="number" min="0" max="100" placeholder="Weight %" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" required /></div>
                    <div class="col-span-2"><input name="max_score" type="number" min="1" max="100" placeholder="Max score" value="100" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                    <div class="col-span-2"><input name="description" placeholder="Description" class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" /></div>
                    <div class="col-span-1"><button class="w-full px-2 py-2 bg-emerald-600 text-white text-xs rounded-lg">Add</button></div>
                </form>
                <div class="mt-4 space-y-2">
                    @foreach ($tender->criteria as $criterion)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                            <div>
                                <span class="text-sm font-medium text-slate-800">{{ $criterion->name }}</span>
                                <span class="text-xs text-slate-500 ml-2">{{ $criterion->weight }}% · max {{ $criterion->max_score }}</span>
                            </div>
                            <form method="POST" action="{{ route('tenders.criteria.destroy', $criterion) }}" onsubmit="return confirm('Remove?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-500">Remove</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="hd-card p-6 h-fit">
            <h3 class="font-semibold text-slate-900 mb-3">Lifecycle</h3>
            <ol class="space-y-3 text-sm">
                <li class="flex items-center gap-2 text-emerald-600"><span class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs">1</span> Draft</li>
                <li class="flex items-center gap-2 {{ $tender->published_at ? 'text-emerald-600' : 'text-slate-400' }}"><span class="w-6 h-6 rounded-full flex items-center justify-center text-xs {{ $tender->published_at ? 'bg-emerald-500 text-white' : 'bg-slate-200' }}">2</span> Published</li>
                <li class="flex items-center gap-2 {{ in_array($tender->status, ['closed', 'under_evaluation', 'awarded']) ? 'text-emerald-600' : 'text-slate-400' }}"><span class="w-6 h-6 rounded-full flex items-center justify-center text-xs {{ in_array($tender->status, ['closed', 'under_evaluation', 'awarded']) ? 'bg-emerald-500 text-white' : 'bg-slate-200' }}">3</span> Closed for bids</li>
                <li class="flex items-center gap-2 {{ $tender->status === 'under_evaluation' ? 'text-emerald-600' : 'text-slate-400' }}"><span class="w-6 h-6 rounded-full flex items-center justify-center text-xs {{ $tender->status === 'under_evaluation' ? 'bg-emerald-500 text-white' : 'bg-slate-200' }}">4</span> Evaluation</li>
                <li class="flex items-center gap-2 {{ $tender->status === 'awarded' ? 'text-emerald-600' : 'text-slate-400' }}"><span class="w-6 h-6 rounded-full flex items-center justify-center text-xs {{ $tender->status === 'awarded' ? 'bg-emerald-500 text-white' : 'bg-slate-200' }}">5</span> Award</li>
            </ol>
        </div>
    </div>
</x-app-layout>
