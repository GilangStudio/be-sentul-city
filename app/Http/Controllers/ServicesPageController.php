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
}