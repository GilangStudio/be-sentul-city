<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CareerPosition;
use App\Models\CareerApplication;
use App\Models\CareerPageSetting;
use Illuminate\Http\Request;
use App\Services\ImageService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class CareerApplicationApiController extends Controller
{
    /**
     * Get career page settings
     */
    public function getPageSettings(): JsonResponse
    {
        try {
            $careerPage = CareerPageSetting::first();
            
            if (!$careerPage || !$careerPage->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Career page is not available'
                ], 404);
            }

            $pageData = [
                'banner' => [
                    'image_url' => $careerPage->banner_image_url,
                    'alt_text' => $careerPage->banner_alt_text,
                    'title' => $careerPage->banner_title,
                ],
                'seo' => [
                    'meta_title' => $careerPage->meta_title_display,
                    'meta_description' => $careerPage->meta_description_display,
                    'meta_keywords' => $careerPage->meta_keywords_display,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $pageData,
                'message' => 'Career page settings retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve career page settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all open career positions
     */
    public function getPositions(): JsonResponse
    {
        try {
            $positions = CareerPosition::applicable()
                                    ->ordered()
                                    ->select('id', 'title', 'slug', 'type', 'location', 'posted_at', 'closing_date')
                                    ->get()
                                    ->map(function ($position) {
                                        return [
                                            'id' => $position->id,
                                            'title' => $position->title,
                                            'slug' => $position->slug,
                                            'type' => $position->type_text,
                                            'location' => $position->location,
                                            'posted_at' => $position->posted_at->format('d F Y'),
                                            'days_posted' => $position->days_posted_text,
                                            'closing_date' => $position->closing_date ? $position->closing_date->format('d F Y') : null,
                                            'days_posted' => $position->days_posted_text,
                                        ];
                                    });

            return response()->json([
                'success' => true,
                'data' => $positions,
                'message' => 'Career positions retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve career positions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific career position details by ID or Slug
     */
    public function getPosition($identifier): JsonResponse
    {
        try {
            // Try to find by ID first, then by slug
            $position = CareerPosition::where('id', $identifier)
                                    ->orWhere('slug', $identifier)
                                    ->first();

            if (!$position) {
                return response()->json([
                    'success' => false,
                    'message' => 'Position not found'
                ], 404);
            }

            if (!$position->isAcceptingApplications()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This position is no longer accepting applications'
                ], 410);
            }

            $positionData = [
                'id' => $position->id,
                'title' => $position->title,
                'slug' => $position->slug,
                'type' => $position->type_text,
                'location' => $position->location,
                'responsibilities' => $position->responsibilities,
                'requirements' => $position->requirements,
                'benefits' => $position->benefits,
                'posted_at' => $position->posted_at->format('d F Y'),
                'closing_date' => $position->closing_date ? $position->closing_date->format('d F Y') : null,
                'days_posted' => $position->days_posted_text,
                // 'applications_count' => $position->applications_count,
            ];

            return response()->json([
                'success' => true,
                'data' => $positionData,
                'message' => 'Career position details retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve position details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit job application
     */
    public function submitApplication(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'career_position_id' => 'required|exists:career_positions,id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'message' => 'nullable|string|max:2000',
                'cv_file' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB
            ], [
                'career_position_id.required' => 'Position is required',
                'career_position_id.exists' => 'Selected position is invalid',
                'name.required' => 'Full name is required',
                'name.max' => 'Name cannot exceed 255 characters',
                'email.required' => 'Email address is required',
                'email.email' => 'Please enter a valid email address',
                'email.max' => 'Email cannot exceed 255 characters',
                'phone.required' => 'Phone number is required',
                'phone.max' => 'Phone number cannot exceed 20 characters',
                'message.max' => 'Message cannot exceed 2000 characters',
                'cv_file.required' => 'CV file is required',
                'cv_file.file' => 'CV must be a valid file',
                'cv_file.mimes' => 'CV must be in PDF, DOC, or DOCX format',
                'cv_file.max' => 'CV file size cannot exceed 5MB',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if position exists and is still open
            $position = CareerPosition::find($request->career_position_id);
            
            if (!$position || !$position->isAcceptingApplications()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This position is no longer accepting applications'
                ], 410);
            }

            // Check if user already applied for this position
            $existingApplication = CareerApplication::where('career_position_id', $request->career_position_id)
                                                  ->where('email', $request->email)
                                                  ->first();

            if ($existingApplication) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already applied for this position with this email address'
                ], 409);
            }

            // Upload CV file
            $cvFilePath = $this->uploadCvFile($request->file('cv_file'), $request->name, $position->title);

            // Create application
            $application = CareerApplication::create([
                'career_position_id' => $request->career_position_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
                'cv_file_path' => $cvFilePath,
                'status' => 'pending',
                'applied_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'application_id' => $application->id,
                    'position_title' => $position->title,
                    'applied_at' => $application->applied_at->format('d M Y H:i'),
                    'status' => 'pending',
                ],
                'message' => 'Your application has been submitted successfully! We will review it and get back to you soon.'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload CV file with proper naming
     */
    private function uploadCvFile($file, $applicantName, $positionTitle): string
    {
        $originalExtension = $file->getClientOriginalExtension();
        $sanitizedName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $applicantName);
        $sanitizedPosition = preg_replace('/[^A-Za-z0-9\-_]/', '_', $positionTitle);
        $timestamp = now()->format('YmdHis');
        $randomString = \Illuminate\Support\Str::random(6);
        
        $fileName = "CV_{$sanitizedName}_{$sanitizedPosition}_{$timestamp}_{$randomString}.{$originalExtension}";
        
        $path = $file->storeAs('career-applications/cv', $fileName, 'public');
        
        return $path;
    }
}