<?php

namespace App\Http\Controllers;

use App\Models\ServiceSection;
use App\Models\ServicesPageSetting;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class ServiceSectionController extends Controller
{
    /**
     * Display service sections index page
     */
    public function index()
    {
        $servicesPage = ServicesPageSetting::first();
        $sections = ServiceSection::ordered()->get();
        
        return view('pages.services.sections.index', compact('servicesPage', 'sections'));
    }

    /**
     * Show create section form
     */
    public function create()
    {
        $servicesPage = ServicesPageSetting::first();
        
        if (!$servicesPage) {
            return redirect()->route('services.index')
                           ->with('error', 'Please create services page settings first.');
        }

        return view('pages.services.sections.create');
    }

    /**
     * Store new service section
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
                'image_alt_text' => 'nullable|string|max:255',
                'layout' => 'required|in:image_left,image_right',
            ], [
                'title.required' => 'Section title is required',
                'title.max' => 'Title cannot exceed 255 characters',
                'description.required' => 'Section description is required',
                'image.required' => 'Section image is required',
                'image.image' => 'File must be an image',
                'image.max' => 'Image size cannot exceed 5MB',
                'image_alt_text.max' => 'Alt text cannot exceed 255 characters',
                'layout.required' => 'Section layout is required',
                'layout.in' => 'Invalid layout selected',
            ]);

        try {
            $imagePath = ImageService::uploadAndCompress(
                $request->file('image'),
                'services-page/sections',
                85,
                1200
            );

            $order = GeneratorService::generateOrder(new ServiceSection());

            ServiceSection::create([
                'title' => $request->title,
                'description' => $request->description,
                'image_path' => $imagePath,
                'image_alt_text' => $request->image_alt_text,
                'layout' => $request->layout,
                'order' => $order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('services.sections.index')
                           ->with('success', 'Service section created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create service section: ' . $e->getMessage());
        }
    }

    /**
     * Show edit section form
     */
    public function edit(ServiceSection $section)
    {
        return view('pages.services.sections.edit', compact('section'));
    }

    /**
     * Update service section
     */
    public function update(Request $request, ServiceSection $section)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_alt_text' => 'nullable|string|max:255',
            'layout' => 'required|in:image_left,image_right',
        ], [
            'title.required' => 'Section title is required',
            'title.max' => 'Title cannot exceed 255 characters',
            'description.required' => 'Section description is required',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 5MB',
            'image_alt_text.max' => 'Alt text cannot exceed 255 characters',
            'layout.required' => 'Section layout is required',
            'layout.in' => 'Invalid layout selected',
        ]);

        try {
            $data = [
                'title' => $request->title,
                'description' => $request->description,
                'image_alt_text' => $request->image_alt_text,
                'layout' => $request->layout,
                'is_active' => $request->has('is_active'),
            ];

            // Update image if new file provided
            if ($request->hasFile('image')) {
                $data['image_path'] = ImageService::updateImage(
                    $request->file('image'),
                    $section->image_path,
                    'services-page/sections',
                    85,
                    1200
                );
            }

            $section->update($data);

            return redirect()->route('services.sections.index')
                           ->with('success', 'Service section updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to update service section: ' . $e->getMessage());
        }
    }

    /**
     * Delete service section
     */
    public function destroy(ServiceSection $section)
    {
        try {
            // Delete associated image
            if ($section->image_path) {
                ImageService::deleteFile($section->image_path);
            }

            $section->delete();

            // Reorder remaining sections
            GeneratorService::reorderAfterDelete(new ServiceSection());

            return redirect()->route('services.sections.index')
                           ->with('success', 'Service section deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('services.sections.index')
                           ->with('error', 'Failed to delete service section: ' . $e->getMessage());
        }
    }

    /**
     * Update sections order via AJAX
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:service_sections,id',
            'orders.*.order' => 'required|integer|min:1',
        ]);

        try {
            foreach ($request->orders as $index => $item) {
                ServiceSection::where('id', $item['id'])
                             ->update(['order' => $index + 1]);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Section order updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update section order: ' . $e->getMessage()
            ], 500);
        }
    }
}