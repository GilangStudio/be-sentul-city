<?php

namespace App\Http\Controllers;

use App\Models\NewResidentsPageSetting;
use App\Models\PracticalInfoCategory;
use App\Models\PracticalInfoPlace;
use App\Models\TransportationItem;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class NewResidentsController extends Controller
{
    /**
     * Display new residents page management
     */
    public function index()
    {
        $newResidentsPage = NewResidentsPageSetting::first();
        $categories = PracticalInfoCategory::withCount('places')->ordered()->get();
        $transportationItems = TransportationItem::ordered()->get();
        
        return view('pages.new-residents.index', compact('newResidentsPage', 'categories', 'transportationItems'));
    }

    /**
     * Update or create new residents page settings
     */
    public function updateOrCreate(Request $request)
    {
        $newResidentsPage = NewResidentsPageSetting::first();
        $isUpdate = !is_null($newResidentsPage);
        
        $request->validate([
            'banner_image' => $isUpdate ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240' : 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'banner_alt_text' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'neighborhood_title' => 'required|string|max:255',
            'neighborhood_description' => 'required|string',
            'neighborhood_image' => $isUpdate ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120' : 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'neighborhood_image_alt_text' => 'nullable|string|max:255',
        ], [
            'banner_image.required' => 'Banner image is required',
            'banner_image.image' => 'Banner must be an image file',
            'banner_image.max' => 'Banner image size cannot exceed 10MB',
            'neighborhood_title.required' => 'Neighborhood guide title is required',
            'neighborhood_description.required' => 'Neighborhood guide description is required',
            'neighborhood_image.required' => 'Neighborhood guide image is required',
            'neighborhood_image.image' => 'Neighborhood image must be an image file',
            'neighborhood_image.max' => 'Neighborhood image size cannot exceed 5MB',
        ]);

        try {
            $data = [
                'banner_alt_text' => $request->banner_alt_text,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'neighborhood_title' => $request->neighborhood_title,
                'neighborhood_description' => $request->neighborhood_description,
                'neighborhood_image_alt_text' => $request->neighborhood_image_alt_text,
                'is_active' => $request->has('is_active'),
            ];

            if ($isUpdate) {
                if ($request->hasFile('banner_image')) {
                    $data['banner_image_path'] = ImageService::updateImage(
                        $request->file('banner_image'),
                        $newResidentsPage->banner_image_path,
                        'new-residents-page/banner',
                        85,
                        1920
                    );
                }
                
                if ($request->hasFile('neighborhood_image')) {
                    $data['neighborhood_image_path'] = ImageService::updateImage(
                        $request->file('neighborhood_image'),
                        $newResidentsPage->neighborhood_image_path,
                        'new-residents-page/neighborhood',
                        85,
                        1200
                    );
                }
                
                $newResidentsPage->update($data);
                $message = 'New residents page settings updated successfully';
            } else {
                $data['banner_image_path'] = ImageService::uploadAndCompress(
                    $request->file('banner_image'),
                    'new-residents-page/banner',
                    85,
                    1920
                );
                
                $data['neighborhood_image_path'] = ImageService::uploadAndCompress(
                    $request->file('neighborhood_image'),
                    'new-residents-page/neighborhood',
                    85,
                    1200
                );
                
                NewResidentsPageSetting::create($data);
                $message = 'New residents page settings created successfully';
            }

            return redirect()->route('new-residents.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to save new residents page settings: ' . $e->getMessage());
        }
    }

    /**
     * Delete new residents page settings
     */
    public function destroy()
    {
        $newResidentsPage = NewResidentsPageSetting::first();
        
        if (!$newResidentsPage) {
            return redirect()->route('new-residents.index')
                           ->with('error', 'New residents page settings not found');
        }

        try {
            // Delete all associated images
            ImageService::deleteFiles([
                $newResidentsPage->banner_image_path,
                $newResidentsPage->neighborhood_image_path,
            ]);

            $newResidentsPage->delete();

            return redirect()->route('new-residents.index')
                           ->with('success', 'New residents page settings deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('new-residents.index')
                           ->with('error', 'Failed to delete new residents page settings: ' . $e->getMessage());
        }
    }
}