<?php
namespace App\Http\Controllers;

use App\Models\AboutFunctionItem;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class AboutFunctionsController extends Controller
{
    public function index(Request $request)
    {
        $query = AboutFunctionItem::query();
        
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
        
        return view('pages.about-us.functions.index', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_alt_text' => 'nullable|string|max:255',
        ], [
            'title.required' => 'Title is required',
            'image.required' => 'Image is required',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 5MB',
        ]);

        try {
            $imagePath = ImageService::uploadAndCompress(
                $request->file('image'),
                'about-us/functions',
                85,
                600
            );

            $order = GeneratorService::generateOrder(new AboutFunctionItem());

            AboutFunctionItem::create([
                'title' => $request->title,
                'description' => $request->description,
                'image_path' => $imagePath,
                'image_alt_text' => $request->image_alt_text,
                'order' => $order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('about-us.functions.index')
                           ->with('success', 'Function item created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to create function item: ' . $e->getMessage());
        }
    }

    public function update(Request $request, AboutFunctionItem $item)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_alt_text' => 'nullable|string|max:255',
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
                    'about-us/functions',
                    85,
                    600
                );
            }

            $item->update($data);

            return redirect()->route('about-us.functions.index')
                           ->with('success', 'Function item updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to update function item: ' . $e->getMessage());
        }
    }

    public function destroy(AboutFunctionItem $item)
    {
        try {
            if ($item->image_path) {
                ImageService::deleteFile($item->image_path);
            }

            $item->delete();
            GeneratorService::reorderAfterDelete(new AboutFunctionItem());

            return redirect()->route('about-us.functions.index')
                           ->with('success', 'Function item deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('about-us.functions.index')
                           ->with('error', 'Failed to delete function item: ' . $e->getMessage());
        }
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:about_function_items,id',
            'orders.*.order' => 'required|integer|min:1',
        ]);

        try {
            foreach ($request->orders as $item) {
                AboutFunctionItem::where('id', $item['id'])
                                ->update(['order' => $item['order']]);
            }

            return response()->json(['success' => true, 'message' => 'Order updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update order: ' . $e->getMessage()]);
        }
    }
}