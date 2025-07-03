<?php

namespace App\Http\Controllers;

use App\Models\ServicesPageSetting;
use App\Models\ServiceSection;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class ServicesPageController extends Controller
{
    /**
     * Display services page management
     */
    public function index()
    {
        $servicesPage = ServicesPageSetting::first();
        $sections = ServiceSection::ordered()->get();
        
        return view('pages.services.index', compact('servicesPage', 'sections'));
    }

    /**
     * Update or create services page settings
     */
    public function updateOrCreate(Request $request)
    {
        $servicesPage = ServicesPageSetting::first();
        $isUpdate = !is_null($servicesPage);
        
        $request->validate(
            [
                'banner_image' => $isUpdate ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240' : 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
                'banner_alt_text' => 'nullable|string|max:255',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'meta_keywords' => 'nullable|string|max:255',
            ], [
                'banner_image.required' => 'Banner image is required',
                'banner_image.image' => 'Banner must be an image file',
                'banner_image.max' => 'Banner image size cannot exceed 10MB',
                'banner_alt_text.max' => 'Alt text cannot exceed 255 characters',
                'meta_title.max' => 'Meta title cannot exceed 255 characters',
                'meta_description.max' => 'Meta description cannot exceed 500 characters',
                'meta_keywords.max' => 'Meta keywords cannot exceed 255 characters',
            ]
        );

        try {
            $data = [
                'banner_alt_text' => $request->banner_alt_text,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'is_active' => $request->has('is_active'),
            ];

            if ($isUpdate) {
                // Update existing record
                if ($request->hasFile('banner_image')) {
                    $data['banner_image_path'] = ImageService::updateImage(
                        $request->file('banner_image'),
                        $servicesPage->banner_image_path,
                        'services-page/banner',
                        85,
                        1920
                    );
                }

                $servicesPage->update($data);
                $message = 'Services page settings updated successfully';
            } else {
                // Create new record
                $data['banner_image_path'] = ImageService::uploadAndCompress(
                    $request->file('banner_image'),
                    'services-page/banner',
                    85,
                    1920
                );

                ServicesPageSetting::create($data);
                $message = 'Services page settings created successfully';
            }

            return redirect()->route('services.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to save services page settings: ' . $e->getMessage());
        }
    }

    /**
     * Show create section form
     */
    public function createSections()
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
    public function storeSections(Request $request)
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

            return redirect()->route('services.index')
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
    public function editSections(ServiceSection $section)
    {
        return view('pages.services.sections.edit', compact('section'));
    }

    /**
     * Update service section
     */
    public function updateSections(Request $request, ServiceSection $section)
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

            return redirect()->route('services.index')
                           ->with('success', 'Service section updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to update service section: ' . $e->getMessage());
        }
    }

    /**
     * Delete service section
     */
    public function destroySections(ServiceSection $section)
    {
        try {
            // Delete associated image
            if ($section->image_path) {
                ImageService::deleteFile($section->image_path);
            }

            $section->delete();

            // Reorder remaining sections
            GeneratorService::reorderAfterDelete(new ServiceSection());

            return redirect()->route('services.index')
                           ->with('success', 'Service section deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('services.index')
                           ->with('error', 'Failed to delete service section: ' . $e->getMessage());
        }
    }

    /**
     * Update sections order via AJAX
     */
    public function updateSectionsOrder(Request $request)
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