<?php

namespace App\Http\Controllers;

use App\Models\ETownSection;
use Illuminate\Http\Request;
use App\Services\ImageService;

class ETownController extends Controller
{
    /**
     * Display E-Town page management
     */
    public function index()
    {
        $etownSection = ETownSection::first();
        
        return view('pages.e-town.index', compact('etownSection'));
    }

    /**
     * Update or create E-Town page settings
     */
    public function updateOrCreate(Request $request)
    {
        $etownSection = ETownSection::first();
        $isUpdate = !is_null($etownSection);
        
        $request->validate([
            'app_mockup_image' => $isUpdate ? 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240' : 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'app_mockup_alt_text' => 'nullable|string|max:255',
            'section_title' => 'required|string|max:255',
            'description' => 'required|string',
            'google_play_url' => 'nullable|url|max:500',
            'app_store_url' => 'nullable|url|max:500',
        ], [
            'app_mockup_image.required' => 'App mockup image is required',
            'app_mockup_image.image' => 'App mockup must be an image file',
            'app_mockup_image.max' => 'App mockup image size cannot exceed 10MB',
            'section_title.required' => 'Section title is required',
            'description.required' => 'Description is required',
            'google_play_url.url' => 'Google Play URL must be a valid URL',
            'app_store_url.url' => 'App Store URL must be a valid URL',
        ]);

        try {
            $data = [
                'app_mockup_alt_text' => $request->app_mockup_alt_text,
                'section_title' => $request->section_title,
                'description' => $request->description,
                'google_play_url' => $request->google_play_url,
                'app_store_url' => $request->app_store_url,
            ];

            if ($isUpdate) {
                if ($request->hasFile('app_mockup_image')) {
                    $data['app_mockup_image_path'] = ImageService::updateImage(
                        $request->file('app_mockup_image'),
                        $etownSection->app_mockup_image_path,
                        'e-town-page/mockup',
                        85,
                        800
                    );
                }
                
                $etownSection->update($data);
                $message = 'E-Town section updated successfully';
            } else {
                $data['app_mockup_image_path'] = ImageService::uploadAndCompress(
                    $request->file('app_mockup_image'),
                    'e-town-page/mockup',
                    85,
                    800
                );
                
                ETownSection::create($data);
                $message = 'E-Town section created successfully';
            }

            return redirect()->route('e-town.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to save E-Town section: ' . $e->getMessage());
        }
    }

    /**
     * Delete E-Town page settings
     */
    public function destroy()
    {
        $etownSection = ETownSection::first();
        
        if (!$etownSection) {
            return redirect()->route('e-town.index')
                           ->with('error', 'E-Town section not found');
        }

        try {
            // Delete app mockup image
            if ($etownSection->app_mockup_image_path) {
                ImageService::deleteFile($etownSection->app_mockup_image_path);
            }

            $etownSection->delete();

            return redirect()->route('e-town.index')
                           ->with('success', 'E-Town section deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('e-town.index')
                           ->with('error', 'Failed to delete E-Town section: ' . $e->getMessage());
        }
    }
}