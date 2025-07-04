<?php

namespace App\Http\Controllers;

use App\Models\PracticalInfoCategory;
use Illuminate\Http\Request;
use App\Services\GeneratorService;

class PracticalInfoCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = PracticalInfoCategory::withCount('places');
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('title', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $categories = $query->ordered()->paginate(15)->appends($request->query());
        
        return view('pages.new-residents.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Category name is required',
            'title.required' => 'Category title is required',
            'name.max' => 'Category name cannot exceed 255 characters',
            'title.max' => 'Category title cannot exceed 255 characters',
            'description.max' => 'Description cannot exceed 1000 characters',
        ]);

        try {
            $slug = GeneratorService::generateSlug(new PracticalInfoCategory(), $request->name);
            $order = GeneratorService::generateOrder(new PracticalInfoCategory());

            PracticalInfoCategory::create([
                'name' => $request->name,
                'slug' => $slug,
                'title' => $request->title,
                'description' => $request->description,
                'order' => $order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('new-residents.categories.index')
                           ->with('success', 'Practical info category created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    public function update(Request $request, PracticalInfoCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Category name is required',
            'title.required' => 'Category title is required',
            'name.max' => 'Category name cannot exceed 255 characters',
            'title.max' => 'Category title cannot exceed 255 characters',
            'description.max' => 'Description cannot exceed 1000 characters',
        ]);

        try {
            $slug = GeneratorService::generateSlug(new PracticalInfoCategory(), $request->name, $category->id);

            $category->update([
                'name' => $request->name,
                'slug' => $slug,
                'title' => $request->title,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('new-residents.categories.index')
                           ->with('success', 'Practical info category updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    public function destroy(PracticalInfoCategory $category)
    {
        try {
            // Check if category has places
            if ($category->places()->count() > 0) {
                return redirect()->route('new-residents.categories.index')
                               ->with('error', 'Cannot delete category that has places');
            }

            $category->delete();

            // Reorder categories
            GeneratorService::reorderAfterDelete(new PracticalInfoCategory());

            return redirect()->route('new-residents.categories.index')
                           ->with('success', 'Practical info category deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('new-residents.categories.index')
                           ->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:practical_info_categories,id',
            'orders.*.order' => 'required|integer|min:1',
        ]);

        try {
            foreach ($request->orders as $item) {
                PracticalInfoCategory::where('id', $item['id'])
                                   ->update(['order' => $item['order']]);
            }

            return response()->json(['success' => true, 'message' => 'Order updated successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update order: ' . $e->getMessage()]);
        }
    }
}