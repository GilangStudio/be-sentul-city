<?php

namespace App\Http\Controllers;

use App\Models\NewsCategory;
use Illuminate\Http\Request;
use App\Services\GeneratorService;

class NewsCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsCategory::withCount('news');
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $categories = $query->ordered()->paginate(15)->appends($request->query());
        
        return view('pages.news.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Category name is required',
            'name.unique' => 'Category name already exists',
            'name.max' => 'Category name cannot exceed 255 characters',
            'description.max' => 'Description cannot exceed 1000 characters',
        ]);

        try {
            $slug = GeneratorService::generateSlug(new NewsCategory(), $request->name);
            $order = GeneratorService::generateOrder(new NewsCategory());

            NewsCategory::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'order' => $order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('news.categories.index')
                           ->with('success', 'Category created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    public function update(Request $request, NewsCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Category name is required',
            'name.unique' => 'Category name already exists',
            'name.max' => 'Category name cannot exceed 255 characters',
            'description.max' => 'Description cannot exceed 1000 characters',
        ]);

        try {
            $slug = GeneratorService::generateSlug(new NewsCategory(), $request->name, $category->id);

            $category->update([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('news.categories.index')
                           ->with('success', 'Category updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    public function destroy(NewsCategory $category)
    {
        try {
            // Check if category has news
            if ($category->news()->count() > 0) {
                return redirect()->route('news.categories.index')
                               ->with('error', 'Cannot delete category that has news articles');
            }

            $category->delete();

            // Reorder categories
            GeneratorService::reorderAfterDelete(new NewsCategory());

            return redirect()->route('news.categories.index')
                           ->with('success', 'Category deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('news.categories.index')
                           ->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:news_categories,id',
            'orders.*.order' => 'required|integer|min:1',
        ]);

        try {
            foreach ($request->orders as $item) {
                NewsCategory::where('id', $item['id'])
                           ->update(['order' => $item['order']]);
            }

            return response()->json(['success' => true, 'message' => 'Order updated successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update order: ' . $e->getMessage()]);
        }
    }
}