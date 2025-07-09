<?php

namespace App\Http\Controllers;

use App\Models\AboutUsPageSetting;
use App\Models\AboutExecutiveSummaryItem;
use App\Models\AboutFunctionItem;
use App\Models\AboutServiceItem;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class AboutUsController extends Controller
{
    public function index()
    {
        $aboutPage = AboutUsPageSetting::first();
        $executiveSummaryItems = AboutExecutiveSummaryItem::ordered()->get();
        $functionItems = AboutFunctionItem::ordered()->get();
        $serviceItems = AboutServiceItem::ordered()->get();

        return view('pages.about-us.index', compact(
            'aboutPage', 
            'executiveSummaryItems', 
            'functionItems', 
            'serviceItems'
        ));
    }

    public function updateOrCreate(Request $request)
    {
        $aboutPage = AboutUsPageSetting::first();
        $isUpdate = !is_null($aboutPage);

        $request->validate([
            'banner_image' => $isUpdate ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240' : 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'banner_alt_text' => 'nullable|string|max:255',
            'home_thumbnail_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'home_thumbnail_alt_text' => 'nullable|string|max:255',
            'company_logo_header' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'company_logo_header_alt_text' => 'nullable|string|max:255',
            'company_logo_footer' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'company_logo_footer_alt_text' => 'nullable|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_description' => 'required|string',
            'vision' => 'required|string',
            'mission' => 'required|string',
            'total_houses' => 'required|integer|min:0',
            'houses_label' => 'required|string|max:100',
            'daily_visitors' => 'required|integer|min:0',
            'visitors_label' => 'required|string|max:100',
            'commercial_areas' => 'required|integer|min:0',
            'commercial_label' => 'required|string|max:100',
            'main_section1_image' => $isUpdate ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120' : 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'main_section1_image_alt_text' => 'nullable|string|max:255',
            'main_section1_title' => 'required|string|max:255',
            'main_section1_description' => 'required|string',
            'main_section2_image' => $isUpdate ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120' : 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'main_section2_image_alt_text' => 'nullable|string|max:255',
            'main_section2_title' => 'required|string|max:255',
            'main_section2_description' => 'required|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'website_url' => 'nullable|url',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ], [
            'banner_image.required' => 'Banner image is required',
            'company_name.required' => 'Company name is required',
            'company_description.required' => 'Company description is required',
            'vision.required' => 'Vision is required',
            'mission.required' => 'Mission is required',
            'main_section1_image.required' => 'Section 1 image is required',
            'main_section1_title.required' => 'Section 1 title is required',
            'main_section1_description.required' => 'Section 1 description is required',
            'main_section2_image.required' => 'Section 2 image is required',
            'main_section2_title.required' => 'Section 2 title is required',
            'main_section2_description.required' => 'Section 2 description is required',
            'home_thumbnail_image.image' => 'Home thumbnail must be an image',
            'home_thumbnail_image.max' => 'Home thumbnail size cannot exceed 5MB',
            'company_logo_header.image' => 'Header logo must be an image',
            'company_logo_header.max' => 'Header logo size cannot exceed 2MB',
            'company_logo_footer.image' => 'Footer logo must be an image',
            'company_logo_footer.max' => 'Footer logo size cannot exceed 2MB',
            'facebook_url.url' => 'Facebook URL must be a valid URL',
            'instagram_url.url' => 'Instagram URL must be a valid URL',
            'youtube_url.url' => 'YouTube URL must be a valid URL',
            'twitter_url.url' => 'Twitter URL must be a valid URL',
            'linkedin_url.url' => 'LinkedIn URL must be a valid URL',
        ]);

        try {
            $data = [
                'banner_alt_text' => $request->banner_alt_text,
                'home_thumbnail_alt_text' => $request->home_thumbnail_alt_text,
                'company_logo_header_alt_text' => $request->company_logo_header_alt_text,
                'company_logo_footer_alt_text' => $request->company_logo_footer_alt_text,
                'company_name' => $request->company_name,
                'company_description' => $request->company_description,
                'vision' => $request->vision,
                'mission' => $request->mission,
                'total_houses' => $request->total_houses,
                'houses_label' => $request->houses_label,
                'daily_visitors' => $request->daily_visitors,
                'visitors_label' => $request->visitors_label,
                'commercial_areas' => $request->commercial_areas,
                'commercial_label' => $request->commercial_label,
                'main_section1_image_alt_text' => $request->main_section1_image_alt_text,
                'main_section1_title' => $request->main_section1_title,
                'main_section1_description' => $request->main_section1_description,
                'main_section2_image_alt_text' => $request->main_section2_image_alt_text,
                'main_section2_title' => $request->main_section2_title,
                'main_section2_description' => $request->main_section2_description,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'website_url' => $request->website_url,
                'facebook_url' => $request->facebook_url,
                'instagram_url' => $request->instagram_url,
                'youtube_url' => $request->youtube_url,
                'twitter_url' => $request->twitter_url,
                'linkedin_url' => $request->linkedin_url,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'is_active' => $request->has('is_active'),
            ];

            if ($isUpdate) {
                // Update existing images
                if ($request->hasFile('banner_image')) {
                    $data['banner_image_path'] = ImageService::updateImage(
                        $request->file('banner_image'),
                        $aboutPage->banner_image_path,
                        'about-us/banner',
                        85,
                        1920
                    );
                }

                if ($request->hasFile('home_thumbnail_image')) {
                    $data['home_thumbnail_image_path'] = ImageService::updateImage(
                        $request->file('home_thumbnail_image'),
                        $aboutPage->home_thumbnail_image_path,
                        'about-us/home-thumbnail',
                        85,
                        800
                    );
                }

                if ($request->hasFile('company_logo_header')) {
                    $data['company_logo_header_path'] = ImageService::updateImage(
                        $request->file('company_logo_header'),
                        $aboutPage->company_logo_header_path,
                        'about-us/logo-header',
                        90,
                        400
                    );
                }

                if ($request->hasFile('company_logo_footer')) {
                    $data['company_logo_footer_path'] = ImageService::updateImage(
                        $request->file('company_logo_footer'),
                        $aboutPage->company_logo_footer_path,
                        'about-us/logo-footer',
                        90,
                        400
                    );
                }

                if ($request->hasFile('main_section1_image')) {
                    $data['main_section1_image_path'] = ImageService::updateImage(
                        $request->file('main_section1_image'),
                        $aboutPage->main_section1_image_path,
                        'about-us/sections',
                        85,
                        1200
                    );
                }

                if ($request->hasFile('main_section2_image')) {
                    $data['main_section2_image_path'] = ImageService::updateImage(
                        $request->file('main_section2_image'),
                        $aboutPage->main_section2_image_path,
                        'about-us/sections',
                        85,
                        1200
                    );
                }

                $aboutPage->update($data);
                $message = 'About Us page updated successfully';
            } else {
                // Create new with required images
                $data['banner_image_path'] = ImageService::uploadAndCompress(
                    $request->file('banner_image'),
                    'about-us/banner',
                    85,
                    1920
                );

                if ($request->hasFile('home_thumbnail_image')) {
                    $data['home_thumbnail_image_path'] = ImageService::uploadAndCompress(
                        $request->file('home_thumbnail_image'),
                        'about-us/home-thumbnail',
                        85,
                        800
                    );
                }

                if ($request->hasFile('company_logo_header')) {
                    $data['company_logo_header_path'] = ImageService::uploadAndCompress(
                        $request->file('company_logo_header'),
                        'about-us/logo-header',
                        90,
                        400
                    );
                }

                if ($request->hasFile('company_logo_footer')) {
                    $data['company_logo_footer_path'] = ImageService::uploadAndCompress(
                        $request->file('company_logo_footer'),
                        'about-us/logo-footer',
                        90,
                        400
                    );
                }

                $data['main_section1_image_path'] = ImageService::uploadAndCompress(
                    $request->file('main_section1_image'),
                    'about-us/sections',
                    85,
                    1200
                );

                $data['main_section2_image_path'] = ImageService::uploadAndCompress(
                    $request->file('main_section2_image'),
                    'about-us/sections',
                    85,
                    1200
                );

                AboutUsPageSetting::create($data);
                $message = 'About Us page created successfully';
            }

            return redirect()->route('about-us.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to save About Us page: ' . $e->getMessage());
        }
    }

    // Executive Summary Items
    public function storeExecutiveSummaryItem(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'icon_alt_text' => 'nullable|string|max:255',
        ], [
            'title.required' => 'Title is required',
            'description.required' => 'Description is required',
            'icon.required' => 'Icon is required',
        ]);

        try {
            $iconPath = ImageService::uploadAndCompress(
                $request->file('icon'),
                'about-us/executive-summary',
                85,
                200
            );

            $order = GeneratorService::generateOrder(new AboutExecutiveSummaryItem());

            AboutExecutiveSummaryItem::create([
                'title' => $request->title,
                'description' => $request->description,
                'icon_path' => $iconPath,
                'icon_alt_text' => $request->icon_alt_text,
                'order' => $order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('about-us.index')
                           ->with('success', 'Executive summary item created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to create executive summary item: ' . $e->getMessage());
        }
    }

    public function updateExecutiveSummaryItem(Request $request, AboutExecutiveSummaryItem $item)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'icon_alt_text' => 'nullable|string|max:255',
        ]);

        try {
            $data = [
                'title' => $request->title,
                'description' => $request->description,
                'icon_alt_text' => $request->icon_alt_text,
                'is_active' => $request->has('is_active'),
            ];

            if ($request->hasFile('icon')) {
                $data['icon_path'] = ImageService::updateImage(
                    $request->file('icon'),
                    $item->icon_path,
                    'about-us/executive-summary',
                    85,
                    200
                );
            }

            $item->update($data);

            return redirect()->route('about-us.index')
                           ->with('success', 'Executive summary item updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to update executive summary item: ' . $e->getMessage());
        }
    }

    public function destroyExecutiveSummaryItem(AboutExecutiveSummaryItem $item)
    {
        try {
            if ($item->icon_path) {
                ImageService::deleteFile($item->icon_path);
            }

            $item->delete();
            GeneratorService::reorderAfterDelete(new AboutExecutiveSummaryItem());

            return redirect()->route('about-us.index')
                           ->with('success', 'Executive summary item deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('about-us.index')
                           ->with('error', 'Failed to delete executive summary item: ' . $e->getMessage());
        }
    }

    // Function Items
    public function storeFunctionItem(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_alt_text' => 'nullable|string|max:255',
        ], [
            'title.required' => 'Title is required',
            'image.required' => 'Image is required',
        ]);

        try {
            $imagePath = ImageService::uploadAndCompress(
                $request->file('image'),
                'about-us/functions',
                85,
                600
            );

            $order = GeneratorService::generateOrder(new AboutFunctionItem());

            AboutFunctionItem::create([
                'title' => $request->title,
                'description' => $request->description,
                'image_path' => $imagePath,
                'image_alt_text' => $request->image_alt_text,
                'order' => $order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('about-us.index')
                           ->with('success', 'Function item created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to create function item: ' . $e->getMessage());
        }
    }

    public function updateFunctionItem(Request $request, AboutFunctionItem $item)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_alt_text' => 'nullable|string|max:255',
        ]);

        try {
            $data = [
                'title' => $request->title,
                'description' => $request->description,
                'image_alt_text' => $request->image_alt_text,
                'is_active' => $request->has('is_active'),
            ];

            if ($request->hasFile('image')) {
                $data['image_path'] = ImageService::updateImage(
                    $request->file('image'),
                    $item->image_path,
                    'about-us/functions',
                    85,
                    600
                );
            }

            $item->update($data);

            return redirect()->route('about-us.index')
                           ->with('success', 'Function item updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to update function item: ' . $e->getMessage());
        }
    }

    public function destroyFunctionItem(AboutFunctionItem $item)
    {
        try {
            if ($item->image_path) {
                ImageService::deleteFile($item->image_path);
            }

            $item->delete();
            GeneratorService::reorderAfterDelete(new AboutFunctionItem());

            return redirect()->route('about-us.index')
                           ->with('success', 'Function item deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('about-us.index')
                           ->with('error', 'Failed to delete function item: ' . $e->getMessage());
        }
    }

    // Service Items
    public function storeServiceItem(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_class' => 'required|string|max:100',
            'icon_color' => 'required|string|in:primary,secondary,success,danger,warning,info,light,dark',
        ], [
            'title.required' => 'Title is required',
            'icon_class.required' => 'Icon class is required',
        ]);

        try {
            $order = GeneratorService::generateOrder(new AboutServiceItem());

            AboutServiceItem::create([
                'title' => $request->title,
                'description' => $request->description,
                'icon_class' => $request->icon_class,
                'icon_color' => $request->icon_color,
                'order' => $order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('about-us.index')
                           ->with('success', 'Service item created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to create service item: ' . $e->getMessage());
        }
    }

    public function updateServiceItem(Request $request, AboutServiceItem $item)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_class' => 'required|string|max:100',
            'icon_color' => 'required|string|in:primary,secondary,success,danger,warning,info,light,dark',
        ]);

        try {
            $item->update([
                'title' => $request->title,
                'description' => $request->description,
                'icon_class' => $request->icon_class,
                'icon_color' => $request->icon_color,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('about-us.index')
                           ->with('success', 'Service item updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to update service item: ' . $e->getMessage());
        }
    }

    public function destroyServiceItem(AboutServiceItem $item)
    {
        try {
            $item->delete();
            GeneratorService::reorderAfterDelete(new AboutServiceItem());

            return redirect()->route('about-us.index')
                           ->with('success', 'Service item deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('about-us.index')
                           ->with('error', 'Failed to delete service item: ' . $e->getMessage());
        }
    }

    // Order Update Methods
    public function updateExecutiveSummaryOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:about_executive_summary_items,id',
            'orders.*.order' => 'required|integer|min:1',
        ]);

        try {
            foreach ($request->orders as $item) {
                AboutExecutiveSummaryItem::where('id', $item['id'])
                                       ->update(['order' => $item['order']]);
            }

            return response()->json(['success' => true, 'message' => 'Order updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update order: ' . $e->getMessage()]);
        }
    }

    public function updateFunctionOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:about_function_items,id',
            'orders.*.order' => 'required|integer|min:1',
        ]);

        try {
            foreach ($request->orders as $item) {
                AboutFunctionItem::where('id', $item['id'])
                                ->update(['order' => $item['order']]);
            }

            return response()->json(['success' => true, 'message' => 'Order updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update order: ' . $e->getMessage()]);
        }
    }

    public function updateServiceOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:about_service_items,id',
            'orders.*.order' => 'required|integer|min:1',
        ]);

        try {
            foreach ($request->orders as $item) {
                AboutServiceItem::where('id', $item['id'])
                               ->update(['order' => $item['order']]);
            }

            return response()->json(['success' => true, 'message' => 'Order updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update order: ' . $e->getMessage()]);
        }
    }
}