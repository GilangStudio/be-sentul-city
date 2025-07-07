<?php

namespace App\Http\Controllers;

use App\Models\PartnershipItem;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class PartnershipItemController extends Controller
{
    /**
     * Display partnership items index
     */
    public function index(Request $request)
    {
        $query = PartnershipItem::query();
        
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
        
        $items = $query->ordered()->paginate(12)->appends($request->query());
        
        return view('pages.partnerships.items.index', compact('items'));
    }

    /**
     * Show create item form
     */
    public function create()
    {
        return view('pages.partnerships.items.create');
    }

    /**
     * Store new partnership item
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_alt_text' => 'nullable|string|max:255',
        ], [
            'title.required' => 'Partnership title is required',
            'description.required' => 'Description is required',
            'image.required' => 'Image is required',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 5MB',
        ]);

        try {
            $imagePath = ImageService::uploadAndCompress(
                $request->file('image'),
                'partnerships/items',
                85,
                800
            );

            $order = GeneratorService::generateOrder(new PartnershipItem());

            PartnershipItem::create([
                'title' => $request->title,
                'description' => $request->description,
                'image_path' => $imagePath,
                'image_alt_text' => $request->image_alt_text,
                'order' => $order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('partnerships.items.index')
                           ->with('success', 'Partnership item created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create partnership item: ' . $e->getMessage());
        }
    }

    /**
     * Show edit item form
     */
    public function edit(PartnershipItem $item)
    {
        return view('pages.partnerships.items.edit', compact('item'));
    }

    /**
     * Update partnership item
     */
    public function update(Request $request, PartnershipItem $item)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_alt_text' => 'nullable|string|max:255',
        ], [
            'title.required' => 'Partnership title is required',
            'description.required' => 'Description is required',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 5MB',
        ]);

        try {
            $data = [
                'title' => $request->title,
                'description' => $request->description,
                'image_alt_text' => $request->image_alt_text,
                'is_active' => $request->has('is_active'),
            ];

            if ($request->hasFile('image')) {
                $data['image_path'] = ImageService::updateImage(
                    $request->file('image'),
                    $item->image_path,
                    'partnerships/items',
                    85,
                    800
                );
            }

            $item->update($data);

            return redirect()->route('partnerships.items.index')
                           ->with('success', 'Partnership item updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update partnership item: ' . $e->getMessage());
        }
    }

    /**
     * Delete partnership item
     */
    public function destroy(PartnershipItem $item)
    {
        try {
            if ($item->image_path) {
                ImageService::deleteFile($item->image_path);
            }

            $item->delete();
            GeneratorService::reorderAfterDelete(new PartnershipItem());

            return redirect()->route('partnerships.items.index')
                           ->with('success', 'Partnership item deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('partnerships.items.index')
                           ->with('error', 'Failed to delete partnership item: ' . $e->getMessage());
        }
    }

    /**
     * Update partnership items order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:partnership_items,id',
            'orders.*.order' => 'required|integer|min:1',
        ]);

        try {
            foreach ($request->orders as $item) {
                PartnershipItem::where('id', $item['id'])
                              ->update(['order' => $item['order']]);
            }

            return response()->json(['success' => true, 'message' => 'Order updated successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update order: ' . $e->getMessage()]);
        }
    }
}