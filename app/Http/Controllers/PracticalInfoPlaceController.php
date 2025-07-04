<?php

namespace App\Http\Controllers;

use App\Models\PracticalInfoPlace;
use App\Models\PracticalInfoCategory;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class PracticalInfoPlaceController extends Controller
{
    public function index(Request $request)
    {
        $query = PracticalInfoPlace::with('category');
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('address', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $places = $query->ordered()->paginate(12)->appends($request->query());
        $categories = PracticalInfoCategory::active()->ordered()->get();
        
        return view('pages.new-residents.places.index', compact('places', 'categories'));
    }

    public function create()
    {
        $categories = PracticalInfoCategory::active()->ordered()->get();
        
        if ($categories->isEmpty()) {
            return redirect()->route('new-residents.categories.index')
                           ->with('error', 'Please create at least one practical info category before adding places.');
        }

        return view('pages.new-residents.places.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:practical_info_categories,id',
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_alt_text' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'map_url' => 'nullable|url',
            'description' => 'nullable|string',
        ], [
            'category_id.required' => 'Category is required',
            'name.required' => 'Place name is required',
            'address.required' => 'Address is required',
            'image.required' => 'Place image is required',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 5MB',
            'map_url.url' => 'Map URL must be a valid URL',
        ]);

        try {
            // Check if selected category is active
            $category = PracticalInfoCategory::findOrFail($request->category_id);
            if (!$category->is_active) {
                return redirect()->back()
                               ->withInput()
                               ->withErrors(['category_id' => 'Selected category is not active.']);
            }

            // Upload image
            $imagePath = ImageService::uploadAndCompress(
                $request->file('image'),
                'new-residents/places',
                85,
                1200
            );

            // Generate order
            $order = GeneratorService::generateOrder(new PracticalInfoPlace());

            // Process tags
            $tags = null;
            if ($request->filled('tags')) {
                $tags = array_map('trim', explode(',', $request->tags));
                $tags = array_filter($tags); // Remove empty tags
            }

            PracticalInfoPlace::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'address' => $request->address,
                'image_path' => $imagePath,
                'image_alt_text' => $request->image_alt_text,
                'tags' => $tags,
                'map_url' => $request->map_url,
                'description' => $request->description,
                'order' => $order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('new-residents.places.index')
                           ->with('success', 'Place created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create place: ' . $e->getMessage());
        }
    }

    public function edit(PracticalInfoPlace $place)
    {
        $categories = PracticalInfoCategory::active()->ordered()->get();
        return view('pages.new-residents.places.edit', compact('place', 'categories'));
    }

    public function update(Request $request, PracticalInfoPlace $place)
    {
        $request->validate([
            'category_id' => 'required|exists:practical_info_categories,id',
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_alt_text' => 'nullable|string|max:255',
            'tags' => 'nullable|string',
            'map_url' => 'nullable|url',
            'description' => 'nullable|string',
        ], [
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Selected category is invalid',
            'name.required' => 'Place name is required',
            'name.max' => 'Place name cannot exceed 255 characters',
            'address.required' => 'Address is required',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 5MB',
            'image_alt_text.max' => 'Alt text cannot exceed 255 characters',
            'map_url.url' => 'Map URL must be a valid URL',
        ]);

        try {
            // Check if selected category is active
            $category = PracticalInfoCategory::findOrFail($request->category_id);
            if (!$category->is_active) {
                return redirect()->back()
                               ->withInput()
                               ->withErrors(['category_id' => 'Selected category is not active.']);
            }

            // Update image if new file provided
            $imagePath = ImageService::updateImage(
                $request->file('image'),
                $place->image_path,
                'new-residents/places',
                85,
                1200
            );

            // Process tags
            $tags = null;
            if ($request->filled('tags')) {
                $tags = array_map('trim', explode(',', $request->tags));
                $tags = array_filter($tags); // Remove empty tags
            }

            $place->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'address' => $request->address,
                'image_path' => $imagePath,
                'image_alt_text' => $request->image_alt_text,
                'tags' => $tags,
                'map_url' => $request->map_url,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->back()
                           ->with('success', 'Place updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update place: ' . $e->getMessage());
        }
    }

    public function destroy(PracticalInfoPlace $place)
    {
        try {
            // Delete image if exists
            if ($place->image_path) {
                ImageService::deleteFile($place->image_path);
            }

            $place->delete();

            return redirect()->route('new-residents.places.index')
                           ->with('success', 'Place deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('new-residents.places.index')
                           ->with('error', 'Failed to delete place: ' . $e->getMessage());
        }
    }
}