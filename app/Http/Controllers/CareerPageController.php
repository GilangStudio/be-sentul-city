<?php

namespace App\Http\Controllers;

use App\Models\CareerPageSetting;
use App\Models\CareerPosition;
use App\Models\CareerApplication;
use Illuminate\Http\Request;
use App\Services\ImageService;

class CareerPageController extends Controller
{
    /**
     * Display career page management
     */
    public function index()
    {
        $careerPage = CareerPageSetting::first();
        $positionsCount = CareerPosition::count();
        $applicationsCount = CareerApplication::count();
        $pendingApplicationsCount = CareerApplication::pending()->count();
        
        return view('pages.careers.index', compact(
            'careerPage', 
            'positionsCount', 
            'applicationsCount',
            'pendingApplicationsCount'
        ));
    }

    /**
     * Update or create career page settings
     */
    public function updateOrCreate(Request $request)
    {
        $careerPage = CareerPageSetting::first();
        $isUpdate = !is_null($careerPage);
        
        $request->validate([
            'banner_image' => $isUpdate ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240' : 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'banner_alt_text' => 'nullable|string|max:255',
            'banner_title' => 'required|string|max:255',
            // 'banner_subtitle' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'hr_email' => 'nullable|email|max:255',
            'hr_phone' => 'nullable|string|max:50',
        ], [
            'banner_image.required' => 'Banner image is required',
            'banner_image.image' => 'Banner must be an image file',
            'banner_image.max' => 'Banner image size cannot exceed 10MB',
            'banner_title.required' => 'Banner title is required',
            'hr_email.email' => 'Please enter a valid email address',
        ]);

        try {
            $data = [
                'banner_alt_text' => $request->banner_alt_text,
                'banner_title' => $request->banner_title,
                // 'banner_subtitle' => $request->banner_subtitle,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'hr_email' => $request->hr_email,
                'hr_phone' => $request->hr_phone,
                'is_active' => $request->has('is_active'),
            ];

            if ($isUpdate) {
                if ($request->hasFile('banner_image')) {
                    $data['banner_image_path'] = ImageService::updateImage(
                        $request->file('banner_image'),
                        $careerPage->banner_image_path,
                        'careers-page/banner',
                        85,
                        1920
                    );
                }
                
                $careerPage->update($data);
                $message = 'Career page settings updated successfully';
            } else {
                $data['banner_image_path'] = ImageService::uploadAndCompress(
                    $request->file('banner_image'),
                    'careers-page/banner',
                    85,
                    1920
                );
                
                CareerPageSetting::create($data);
                $message = 'Career page settings created successfully';
            }

            return redirect()->route('careers.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to save career page settings: ' . $e->getMessage());
        }
    }

    /**
     * Delete career page settings
     */
    public function destroy()
    {
        $careerPage = CareerPageSetting::first();
        
        if (!$careerPage) {
            return redirect()->route('careers.index')
                           ->with('error', 'Career page settings not found');
        }

        try {
            // Delete banner image
            if ($careerPage->banner_image_path) {
                ImageService::deleteFile($careerPage->banner_image_path);
            }

            $careerPage->delete();

            return redirect()->route('careers.index')
                           ->with('success', 'Career page settings deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('careers.index')
                           ->with('error', 'Failed to delete career page settings: ' . $e->getMessage());
        }
    }
}