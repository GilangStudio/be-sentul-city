<?php

namespace App\Http\Controllers;

use App\Models\TransportationItem;
use Illuminate\Http\Request;
use App\Services\GeneratorService;

class TransportationItemController extends Controller
{
    public function index(Request $request)
    {
        $query = TransportationItem::query();
        
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
        
        $transportationItems = $query->ordered()->paginate(15)->appends($request->query());
        
        return view('pages.new-residents.transportation.index', compact('transportationItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ], [
            'title.required' => 'Transportation title is required',
            'title.max' => 'Title cannot exceed 255 characters',
            'description.required' => 'Description is required',
            'description.max' => 'Description cannot exceed 1000 characters',
        ]);

        try {
            $order = GeneratorService::generateOrder(new TransportationItem());

            TransportationItem::create([
                'title' => $request->title,
                'description' => $request->description,
                'order' => $order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('new-residents.transportation.index')
                           ->with('success', 'Transportation item created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to create transportation item: ' . $e->getMessage());
        }
    }

    public function update(Request $request, TransportationItem $transportationItem)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ], [
            'title.required' => 'Transportation title is required',
            'title.max' => 'Title cannot exceed 255 characters',
            'description.required' => 'Description is required',
            'description.max' => 'Description cannot exceed 1000 characters',
        ]);

        try {
            $transportationItem->update([
                'title' => $request->title,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('new-residents.transportation.index')
                           ->with('success', 'Transportation item updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to update transportation item: ' . $e->getMessage());
        }
    }

    public function destroy(TransportationItem $transportationItem)
    {
        try {
            $transportationItem->delete();

            // Reorder remaining items
            GeneratorService::reorderAfterDelete(new TransportationItem());

            return redirect()->route('new-residents.transportation.index')
                           ->with('success', 'Transportation item deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('new-residents.transportation.index')
                           ->with('error', 'Failed to delete transportation item: ' . $e->getMessage());
        }
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:transportation_items,id',
            'orders.*.order' => 'required|integer|min:1',
        ]);

        try {
            foreach ($request->orders as $item) {
                TransportationItem::where('id', $item['id'])
                                ->update(['order' => $item['order']]);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Transportation order updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update transportation order: ' . $e->getMessage()
            ], 500);
        }
    }
}