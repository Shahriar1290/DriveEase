<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = VehicleCategory::withCount('vehicles')
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_name' => 'required|string|max:255',
            'description'   => 'nullable|string',
            'icon'          => 'nullable|string|max:100',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'sort_order'    => 'nullable|integer',
        ]);

        if (VehicleCategory::where('category_name', $data['category_name'])->exists()) {
            return back()->withErrors(['category_name' => 'This category name already exists.'])->withInput();
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        VehicleCategory::create(array_merge($data, [
            'slug' => Str::slug($data['category_name']),
            'image' => $imagePath,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $data['sort_order'] ?? 0,
        ]));

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(int $id)
    {
        $category = VehicleCategory::findOrFail($id);
        return view('admin.categories.create', compact('category'));
    }

    public function update(Request $request, int $id)
    {
        $category = VehicleCategory::findOrFail($id);

        $data = $request->validate([
            'category_name' => 'required|string|max:255',
            'description'   => 'nullable|string',
            'icon'          => 'nullable|string|max:100',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'sort_order'    => 'nullable|integer',
        ]);

        if (VehicleCategory::where('category_name', $data['category_name'])->where('id', '!=', $id)->exists()) {
            return back()->withErrors(['category_name' => 'This category name already exists.'])->withInput();
        }

        $imagePath = $category->image;
        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category->update(array_merge($data, [
            'image' => $imagePath,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $data['sort_order'] ?? 0,
        ]));

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(int $id)
    {
        $category = VehicleCategory::findOrFail($id);

        if ($category->vehicles()->count() > 0) {
            return back()->with('error', 'Cannot delete category with vehicles.');
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}
