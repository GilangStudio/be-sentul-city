<?php

namespace App\Http\Controllers;

use App\Models\HomePageSetting;
use App\Models\HomePageBanner;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class HomePageController extends Controller
{
    /**
     * Display homepage management
     */
    public function index()
    {
        $homePageSettings = HomePageSetting::first();
        $banners = HomePageBanner::ordered()->get();
        
        return view('pages.home-page.index', compact('homePageSettings', 'banners'));
    }

    /**
     * Update or create homepage SEO settings
     */
    public function updateSeoSettings(Request $request)
    {
        $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ], [
            'meta_title.max' => 'Meta title cannot exceed 255 characters',
            'meta_description.max' => 'Meta description cannot exceed 500 characters',
            'meta_keywords.max' => 'Meta keywords cannot exceed 255 characters',
        ]);

        try {
            $homePageSettings = HomePageSetting::first();
            
            if ($homePageSettings) {
                $homePageSettings->update([
                    'meta_title' => $request->meta_title,
                    'meta_description' => $request->meta_description,
                    'meta_keywords' => $request->meta_keywords,
                ]);
                $message = 'Homepage SEO settings updated successfully';
            } else {
                HomePageSetting::create([
                    'meta_title' => $request->meta_title,
                    'meta_description' => $request->meta_description,
                    'meta_keywords' => $request->meta_keywords,
                ]);
                $message = 'Homepage SEO settings created successfully';
            }

            return redirect()->route('home-page.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to save SEO settings: ' . $e->getMessage());
        }
    }

    /**
     * Store new banner
     */
    public function storeBanner(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url|max:500',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240', // 10MB
            'image_alt_text' => 'nullable|string|max:255',
        ], [
            'image.required' => 'Banner image is required',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 10MB',
            'button_url.url' => 'Button URL must be a valid URL',
            'title.max' => 'Title cannot exceed 255 characters',
            'button_text.max' => 'Button text cannot exceed 100 characters',
        ]);

        try {
            // Upload image
            $imagePath = ImageService::uploadAndCompress(
                $request->file('image'),
                'homepage/banners',
                85,
                1920
            );

            // Generate order
            $order = GeneratorService::generateOrder(new HomePageBanner());

            HomePageBanner::create([
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'button_text' => $request->button_text,
                'button_url' => $request->button_url,
                'image_path' => $imagePath,
                'image_alt_text' => $request->image_alt_text,
                'order' => $order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('home-page.index')
                           ->with('success', 'Banner created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to create banner: ' . $e->getMessage());
        }
    }

    /**
     * Update banner
     */
    public function updateBanner(Request $request, HomePageBanner $banner)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|url|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'image_alt_text' => 'nullable|string|max:255',
        ], [
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 10MB',
            'button_url.url' => 'Button URL must be a valid URL',
            'title.max' => 'Title cannot exceed 255 characters',
            'button_text.max' => 'Button text cannot exceed 100 characters',
        ]);

        try {
            // Update image if new file provided
            $imagePath = ImageService::updateImage(
                $request->file('image'),
                $banner->image_path,
                'homepage/banners',
                85,
                1920
            );

            $banner->update([
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'button_text' => $request->button_text,
                'button_url' => $request->button_url,
                'image_path' => $imagePath,
                'image_alt_text' => $request->image_alt_text,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('home-page.index')
                           ->with('success', 'Banner updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to update banner: ' . $e->getMessage());
        }
    }

    /**
     * Delete banner
     */
    public function destroyBanner(HomePageBanner $banner)
    {
        try {
            // Delete image if exists
            if ($banner->image_path) {
                ImageService::deleteFile($banner->image_path);
            }

            $banner->delete();

            // Reorder remaining banners
            GeneratorService::reorderAfterDelete(new HomePageBanner());

            return redirect()->route('home-page.index')
                           ->with('success', 'Banner deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('home-page.index')
                           ->with('error', 'Failed to delete banner: ' . $e->getMessage());
        }
    }

    /**
     * Update banners order
     */
    public function updateBannerOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:home_page_banners,id',
            'orders.*.order' => 'required|integer|min:1',
        ]);

        try {
            foreach ($request->orders as $item) {
                HomePageBanner::where('id', $item['id'])
                             ->update(['order' => $item['order']]);
            }

            return response()->json(['success' => true, 'message' => 'Banner order updated successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update banner order: ' . $e->getMessage()]);
        }
    }
}