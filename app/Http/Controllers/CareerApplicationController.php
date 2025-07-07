<?php

namespace App\Http\Controllers;

use App\Models\CareerApplication;
use App\Models\CareerPosition;
use Illuminate\Http\Request;
use App\Services\ImageService;

class CareerApplicationController extends Controller
{
    /**
     * Display career applications for a specific position
     */
    public function index(Request $request, CareerPosition $position)
    {
        $query = $position->applications();
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $applications = $query->recent()->paginate(15)->appends($request->query());
        
        return view('pages.careers.applications.index', compact('position', 'applications'));
    }

    /**
     * Show application details
     */
    public function show(CareerPosition $position, CareerApplication $application)
    {
        // Ensure application belongs to position
        if ($application->career_position_id !== $position->id) {
            abort(404);
        }

        return view('pages.careers.applications.show', compact('position', 'application'));
    }

    /**
     * Update application status
     */
    public function updateStatus(Request $request, CareerPosition $position, CareerApplication $application)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,shortlisted,rejected,hired',
        ]);

        try {
            $application->update([
                'status' => $request->status,
            ]);

            return redirect()->back()
                           ->with('success', 'Application status updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to update application status: ' . $e->getMessage());
        }
    }

    /**
     * Delete application
     */
    public function destroy(CareerPosition $position, CareerApplication $application)
    {
        try {
            // Delete CV file if exists
            if ($application->cv_file_path) {
                ImageService::deleteFile($application->cv_file_path);
            }

            $application->delete();

            return redirect()->route('careers.positions.applications.index', $position)
                           ->with('success', 'Application deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('careers.positions.applications.index', $position)
                           ->with('error', 'Failed to delete application: ' . $e->getMessage());
        }
    }

    /**
     * Download CV file
     */
    public function downloadCv(CareerPosition $position, CareerApplication $application)
    {
        if (!$application->cv_file_path) {
            abort(404);
        }

        $filePath = storage_path('app/public/' . $application->cv_file_path);
        
        if (!file_exists($filePath)) {
            abort(404);
        }

        $fileName = $application->name . '_CV_' . $position->title . '.pdf';
        
        return response()->download($filePath, $fileName);
    }

    /**
     * Bulk update applications status
     */
    public function bulkUpdateStatus(Request $request, CareerPosition $position)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:career_applications,id',
            'status' => 'required|in:pending,reviewed,shortlisted,rejected,hired',
        ]);

        try {
            CareerApplication::whereIn('id', $request->application_ids)
                            ->where('career_position_id', $position->id)
                            ->update(['status' => $request->status]);

            $count = count($request->application_ids);
            
            return response()->json([
                'success' => true, 
                'message' => "{$count} applications updated successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update applications: ' . $e->getMessage()
            ]);
        }
    }
}