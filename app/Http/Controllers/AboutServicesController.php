<?php
// app/Http/Controllers/AboutServicesController.php

namespace App\Http\Controllers;

use App\Models\AboutServiceItem;
use Illuminate\Http\Request;
use App\Services\GeneratorService;

class AboutServicesController extends Controller
{
    public function index(Request $request)
    {
        $query = AboutServiceItem::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $items = $query->ordered()->paginate(15)->appends($request->query());
        
        return view('pages.about-us.services.index', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_class' => 'required|string|max:100',
            'icon_color' => 'required|string|in:primary,secondary,success,danger,warning,info,light,dark',
        ], [
            'title.required' => 'Title is required',
            'icon_class.required' => 'Icon class is required',
            'icon_color.required' => 'Icon color is required',
        ]);

        try {
            $order = GeneratorService::generateOrder(new AboutServiceItem());

            AboutServiceItem::create([
                'title' => $request->title,
                'description' => $request->description,
                'icon_class' => $request->icon_class,
                'icon_color' => $request->icon_color,
                'order' => $order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('about-us.services.index')
                           ->with('success', 'Service item created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to create service item: ' . $e->getMessage());
        }
    }

    public function update(Request $request, AboutServiceItem $item)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_class' => 'required|string|max:100',
            'icon_color' => 'required|string|in:primary,secondary,success,danger,warning,info,light,dark',
        ]);

        try {
            $item->update([
                'title' => $request->title,
                'description' => $request->description,
                'icon_class' => $request->icon_class,
                'icon_color' => $request->icon_color,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('about-us.services.index')
                           ->with('success', 'Service item updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to update service item: ' . $e->getMessage());
        }
    }

    public function destroy(AboutServiceItem $item)
    {
        try {
            $item->delete();
            GeneratorService::reorderAfterDelete(new AboutServiceItem());

            return redirect()->route('about-us.services.index')
                           ->with('success', 'Service item deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('about-us.services.index')
                           ->with('error', 'Failed to delete service item: ' . $e->getMessage());
        }
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:about_service_items,id',
            'orders.*.order' => 'required|integer|min:1',
        ]);

        try {
            foreach ($request->orders as $item) {
                AboutServiceItem::where('id', $item['id'])
                               ->update(['order' => $item['order']]);
            }

            return response()->json(['success' => true, 'message' => 'Order updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update order: ' . $e->getMessage()]);
        }
    }
}