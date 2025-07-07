<?php

namespace App\Http\Controllers;

use App\Models\CareerPosition;
use Illuminate\Http\Request;
use App\Services\GeneratorService;

class CareerPositionController extends Controller
{
    /**
     * Display career positions
     */
    public function index(Request $request)
    {
        $query = CareerPosition::withCount('applications');
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('location', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        $positions = $query->ordered()->paginate(15)->appends($request->query());
        
        return view('pages.careers.positions.index', compact('positions'));
    }

    /**
     * Show create position form
     */
    public function create()
    {
        return view('pages.careers.positions.create');
    }

    /**
     * Store new career position
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:full-time,part-time,contract,internship',
            'location' => 'required|string|max:255',
            'responsibilities' => 'required|string',
            'requirements' => 'required|string',
            'benefits' => 'nullable|string',
            'posted_at' => 'required|date',
            'closing_date' => 'nullable|date|after:posted_at',
        ], [
            'title.required' => 'Position title is required',
            'type.required' => 'Employment type is required',
            'location.required' => 'Work location is required',
            'responsibilities.required' => 'Job responsibilities are required',
            'requirements.required' => 'Job requirements are required',
            'posted_at.required' => 'Posted date is required',
            'closing_date.after' => 'Closing date must be after posted date',
        ]);

        try {
            $slug = GeneratorService::generateSlug(new CareerPosition(), $request->title);
            $order = GeneratorService::generateOrder(new CareerPosition());

            CareerPosition::create([
                'title' => $request->title,
                'slug' => $slug,
                'type' => $request->type,
                'location' => $request->location,
                'responsibilities' => $request->responsibilities,
                'requirements' => $request->requirements,
                'benefits' => $request->benefits,
                'posted_at' => $request->posted_at,
                'closing_date' => $request->closing_date,
                'order' => $order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('careers.positions.index')
                           ->with('success', 'Career position created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create career position: ' . $e->getMessage());
        }
    }

    /**
     * Show edit position form
     */
    public function edit(CareerPosition $position)
    {
        return view('pages.careers.positions.edit', compact('position'));
    }

    /**
     * Update career position
     */
    public function update(Request $request, CareerPosition $position)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:full-time,part-time,contract,internship',
            'location' => 'required|string|max:255',
            'responsibilities' => 'required|string',
            'requirements' => 'required|string',
            'benefits' => 'nullable|string',
            'posted_at' => 'required|date',
            'closing_date' => 'nullable|date|after:posted_at',
        ], [
            'title.required' => 'Position title is required',
            'type.required' => 'Employment type is required',
            'location.required' => 'Work location is required',
            'responsibilities.required' => 'Job responsibilities are required',
            'requirements.required' => 'Job requirements are required',
            'posted_at.required' => 'Posted date is required',
            'closing_date.after' => 'Closing date must be after posted date',
        ]);

        try {
            $slug = GeneratorService::generateSlug(new CareerPosition(), $request->title, $position->id);

            $position->update([
                'title' => $request->title,
                'slug' => $slug,
                'type' => $request->type,
                'location' => $request->location,
                'responsibilities' => $request->responsibilities,
                'requirements' => $request->requirements,
                'benefits' => $request->benefits,
                'posted_at' => $request->posted_at,
                'closing_date' => $request->closing_date,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('careers.positions.index')
                           ->with('success', 'Career position updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update career position: ' . $e->getMessage());
        }
    }

    /**
     * Delete career position (only if no applications)
     */
    public function destroy(CareerPosition $position)
    {
        try {
            if (!$position->canDelete()) {
                return redirect()->route('careers.positions.index')
                               ->with('error', 'Cannot delete position that has job applications');
            }

            $position->delete();

            // Reorder remaining positions
            GeneratorService::reorderAfterDelete(new CareerPosition());

            return redirect()->route('careers.positions.index')
                           ->with('success', 'Career position deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('careers.positions.index')
                           ->with('error', 'Failed to delete career position: ' . $e->getMessage());
        }
    }

    /**
     * Update positions order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:career_positions,id',
            'orders.*.order' => 'required|integer|min:1',
        ]);

        try {
            foreach ($request->orders as $item) {
                CareerPosition::where('id', $item['id'])
                             ->update(['order' => $item['order']]);
            }

            return response()->json(['success' => true, 'message' => 'Order updated successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update order: ' . $e->getMessage()]);
        }
    }
}