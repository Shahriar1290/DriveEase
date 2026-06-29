<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 15;
        $page    = max(1, (int) $request->get('page', 1));
        $offset  = ($page - 1) * $perPage;

        $total = DB::select("SELECT COUNT(*) AS total FROM vehicle_categories")[0]->total;

        $rows = DB::select("
            SELECT c.*, (SELECT COUNT(*) FROM vehicles v WHERE v.category_id = c.id) AS vehicles_count
            FROM vehicle_categories c
            ORDER BY c.sort_order ASC
            LIMIT {$perPage} OFFSET {$offset}
        ");

        $categories = new \Illuminate\Pagination\LengthAwarePaginator(
            $rows, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

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

        $exists = DB::select("SELECT id FROM vehicle_categories WHERE category_name = ?", [$data['category_name']]);
        if (!empty($exists)) {
            return back()->withErrors(['category_name' => 'This category name already exists.'])->withInput();
        }

        $slug      = Str::slug($data['category_name']);
        $isActive  = $request->boolean('is_active', true) ? 1 : 0;
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        DB::insert("
            INSERT INTO vehicle_categories (category_name, slug, description, icon, image, is_active, sort_order, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $data['category_name'], $slug, $data['description'] ?? null, $data['icon'] ?? null,
            $imagePath, $isActive, $data['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(int $id)
    {
        $category = DB::select("SELECT * FROM vehicle_categories WHERE id = ? LIMIT 1", [$id]);
        abort_if(empty($category), 404);
        return view('admin.categories.edit', ['category' => $category[0]]);
    }

    public function update(Request $request, int $id)
    {
        $existing = DB::select("SELECT * FROM vehicle_categories WHERE id = ? LIMIT 1", [$id]);
        abort_if(empty($existing), 404);
        $existing = $existing[0];

        $data = $request->validate([
            'category_name' => 'required|string|max:255',
            'description'   => 'nullable|string',
            'icon'          => 'nullable|string|max:100',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'sort_order'    => 'nullable|integer',
        ]);

        $nameTaken = DB::select(
            "SELECT id FROM vehicle_categories WHERE category_name = ? AND id != ?",
            [$data['category_name'], $id]
        );
        if (!empty($nameTaken)) {
            return back()->withErrors(['category_name' => 'This category name already exists.'])->withInput();
        }

        $isActive  = $request->boolean('is_active') ? 1 : 0;
        $imagePath = $existing->image;

        if ($request->hasFile('image')) {
            if ($existing->image) {
                Storage::disk('public')->delete($existing->image);
            }
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        DB::update("
            UPDATE vehicle_categories SET
                category_name = ?, description = ?, icon = ?, image = ?, is_active = ?, sort_order = ?, updated_at = NOW()
            WHERE id = ?
        ", [
            $data['category_name'], $data['description'] ?? null, $data['icon'] ?? null,
            $imagePath, $isActive, $data['sort_order'] ?? 0, $id,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(int $id)
    {
        $vehicleCount = DB::select("SELECT COUNT(*) AS total FROM vehicles WHERE category_id = ?", [$id])[0]->total;
        if ($vehicleCount > 0) {
            return back()->with('error', 'Cannot delete category with vehicles.');
        }

        $category = DB::select("SELECT * FROM vehicle_categories WHERE id = ? LIMIT 1", [$id]);
        if (!empty($category) && $category[0]->image) {
            Storage::disk('public')->delete($category[0]->image);
        }

        DB::delete("DELETE FROM vehicle_categories WHERE id = ?", [$id]);

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}
