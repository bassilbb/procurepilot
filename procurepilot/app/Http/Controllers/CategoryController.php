<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('organization_id', currentOrganization()->id)
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'code'        => ['nullable', 'string', 'max:60'],
            'type'        => ['required', 'in:goods,services,works'],
            'description' => ['nullable', 'string'],
        ]);

        $data['organization_id'] = currentOrganization()->id;

        $category = Category::create($data);

        AuditLog::record('category_created', $category, [], $category->toArray());

        return back()->with('success', 'Category added.');
    }

    public function update(Request $request, Category $category)
    {
        abort_unless($category->organization_id === currentOrganization()->id, 403);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'code'        => ['nullable', 'string', 'max:60'],
            'type'        => ['required', 'in:goods,services,works'],
            'description' => ['nullable', 'string'],
        ]);

        $before = $category->toArray();
        $category->update($data);

        AuditLog::record('category_updated', $category, $before, $category->toArray());

        return back()->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        abort_unless($category->organization_id === currentOrganization()->id, 403);

        AuditLog::record('category_removed', $category, $category->toArray(), []);
        $category->delete();

        return back()->with('success', 'Category removed.');
    }
}
