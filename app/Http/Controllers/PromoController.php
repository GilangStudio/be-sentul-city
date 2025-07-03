<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class PromoController extends Controller
{
    public function index(Request $request)
    {
        $query = Promo::query();
        
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
        
        $promos = $query->ordered()->paginate(12)->appends($request->query());
        
        return view('pages.promos.index', compact('promos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB
        ], [
            'image.required' => 'Promo image is required',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 5MB',
            'title.max' => 'Title cannot exceed 255 characters',
            'description.max' => 'Description cannot exceed 1000 characters',
        ]);

        try {
            // Upload image
            $imagePath = ImageService::uploadAndCompress(
                $request->file('image'),
                'promos',
                85,
                1200
            );

            // Generate order - get the next available order number
            $maxOrder = Promo::max('order') ?? 0;
            $order = $maxOrder + 1;

            Promo::create([
                'title' => $request->title,
                'description' => $request->description,
                'image_path' => $imagePath,
                'order' => $order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('promos.index')
                           ->with('success', 'Promo created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to create promo: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Promo $promo)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 5MB',
            'title.max' => 'Title cannot exceed 255 characters',
            'description.max' => 'Description cannot exceed 1000 characters',
        ]);

        try {
            // Update image if new file provided
            $imagePath = ImageService::updateImage(
                $request->file('image'),
                $promo->image_path,
                'promos',
                85,
                1200
            );

            $promo->update([
                'title' => $request->title,
                'description' => $request->description,
                'image_path' => $imagePath,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('promos.index')
                           ->with('success', 'Promo updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to update promo: ' . $e->getMessage());
        }
    }

    public function destroy(Promo $promo)
    {
        try {
            // Delete image if exists
            if ($promo->image_path) {
                ImageService::deleteFile($promo->image_path);
            }

            $promo->delete();

            // Reorder all remaining promos to fill gaps
            $this->reorderAllPromos();

            return redirect()->route('promos.index')
                           ->with('success', 'Promo deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('promos.index')
                           ->with('error', 'Failed to delete promo: ' . $e->getMessage());
        }
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:promos,id',
            'orders.*.order' => 'required|integer|min:1',
        ]);

        try {
            // Get current page parameters to maintain context
            $currentPage = $request->input('current_page', 1);
            $perPage = 12; // Same as in index method
            
            // Calculate offset for current page
            $offset = ($currentPage - 1) * $perPage;
            
            // Update orders for items in current page
            foreach ($request->orders as $index => $item) {
                $newOrder = $offset + $index + 1;
                Promo::where('id', $item['id'])
                     ->update(['order' => $newOrder]);
            }

            // Reorder all items to ensure no gaps in ordering
            $this->reorderAllPromos();

            return response()->json(['success' => true, 'message' => 'Order updated successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update order: ' . $e->getMessage()]);
        }
    }

    /**
     * Reorder all promos to ensure sequential ordering
     */
    private function reorderAllPromos()
    {
        $promos = Promo::orderBy('order')->orderBy('created_at')->get();
        
        foreach ($promos as $index => $promo) {
            $promo->update(['order' => $index + 1]);
        }
    }
}