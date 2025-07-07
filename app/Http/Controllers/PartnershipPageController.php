<?php

namespace App\Http\Controllers;

use App\Models\PartnershipPageSetting;
use App\Models\PartnershipItem;
use Illuminate\Http\Request;
use App\Services\ImageService;

class PartnershipPageController extends Controller
{
    /**
     * Display partnership page management
     */
    public function index()
    {
        $partnershipPage = PartnershipPageSetting::first();
        $itemsCount = PartnershipItem::count();
        
        return view('pages.partnerships.index', compact('partnershipPage', 'itemsCount'));
    }

    /**
     * Update or create partnership page settings
     */
    public function updateOrCreate(Request $request)
    {
        $partnershipPage = PartnershipPageSetting::first();
        $isUpdate = !is_null($partnershipPage);
        
        $request->validate([
            'banner_image' => $isUpdate ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240' : 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'banner_alt_text' => 'nullable|string|max:255',
            'banner_title' => 'required|string|max:255',
            // 'banner_subtitle' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ], [
            'banner_image.required' => 'Banner image is required',
            'banner_image.image' => 'Banner must be an image file',
            'banner_image.max' => 'Banner image size cannot exceed 10MB',
            'banner_title.required' => 'Banner title is required',
        ]);

        try {
            $data = [
                'banner_alt_text' => $request->banner_alt_text,
                'banner_title' => $request->banner_title,
                // 'banner_subtitle' => $request->banner_subtitle,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'is_active' => $request->has('is_active'),
            ];

            if ($isUpdate) {
                if ($request->hasFile('banner_image')) {
                    $data['banner_image_path'] = ImageService::updateImage(
                        $request->file('banner_image'),
                        $partnershipPage->banner_image_path,
                        'partnerships-page/banner',
                        85,
                        1920
                    );
                }
                
                $partnershipPage->update($data);
                $message = 'Partnership page settings updated successfully';
            } else {
                $data['banner_image_path'] = ImageService::uploadAndCompress(
                    $request->file('banner_image'),
                    'partnerships-page/banner',
                    85,
                    1920
                );
                
                PartnershipPageSetting::create($data);
                $message = 'Partnership page settings created successfully';
            }

            return redirect()->route('partnerships.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to save partnership page settings: ' . $e->getMessage());
        }
    }

    /**
     * Delete partnership page settings
     */
    public function destroy()
    {
        $partnershipPage = PartnershipPageSetting::first();
        
        if (!$partnershipPage) {
            return redirect()->route('partnerships.index')
                           ->with('error', 'Partnership page settings not found');
        }

        try {
            // Delete banner image
            if ($partnershipPage->banner_image_path) {
                ImageService::deleteFile($partnershipPage->banner_image_path);
            }

            $partnershipPage->delete();

            return redirect()->route('partnerships.index')
                           ->with('success', 'Partnership page settings deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('partnerships.index')
                           ->with('error', 'Failed to delete partnership page settings: ' . $e->getMessage());
        }
    }
}