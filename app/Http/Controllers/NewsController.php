<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\GeneratorService;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::with('category');
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('content', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Order by published_at and created_at
        $news = $query->orderBy('published_at', 'desc')
                     ->orderBy('created_at', 'desc')
                     ->paginate(10)
                     ->appends($request->query());

        // Get categories for filter
        $categories = NewsCategory::active()->ordered()->get();
        
        return view('pages.news.index', compact('news', 'categories'));
    }

    public function create()
    {
        $categories = NewsCategory::active()->ordered()->get();
        
        if ($categories->isEmpty()) {
            return redirect()->route('news.categories.index')
                           ->with('error', 'Please create at least one news category before adding news articles.');
        }

        return view('pages.news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:news_categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB
            'published_at' => 'nullable|date',
            'status' => 'required|in:draft,published',
            'is_featured' => 'nullable|boolean',
        ], [
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Selected category is invalid',
            'title.required' => 'Title is required',
            'title.unique' => 'Title already exists',
            'title.max' => 'Title cannot exceed 255 characters',
            'content.required' => 'Content is required',
            'meta_title.max' => 'Meta title cannot exceed 255 characters',
            'meta_description.max' => 'Meta description cannot exceed 500 characters',
            'meta_keywords.max' => 'Meta keywords cannot exceed 255 characters',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 5MB',
            'published_at.date' => 'Published date must be a valid date',
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',
        ]);

        try {
            // Check if selected category is active
            $category = NewsCategory::findOrFail($request->category_id);
            if (!$category->is_active) {
                return redirect()->back()
                               ->withInput()
                               ->withErrors(['category_id' => 'Selected category is not active.']);
            }

            // Generate slug
            $slug = GeneratorService::generateSlug(new News(), $request->title);

            // Upload image if provided
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = ImageService::uploadAndCompress(
                    $request->file('image'), 
                    'news', 
                    85, 
                    1200
                );
            }

            if ($request->has('is_featured') && $request->is_featured && $request->status !== 'published') {
                return redirect()->back()
                               ->withInput()
                               ->withErrors(['is_featured' => 'News must be published to be featured on home page.']);
            }
            
            if ($request->has('is_featured') && $request->is_featured && $request->status === 'published') {
                News::where('is_featured', true)->update(['is_featured' => false]);
            }
            
            $publishedAt = null;
            $isFeatured = false;
            
            if ($request->status === 'published') {
                $publishedAt = $request->published_at ? $request->published_at : now();
                $isFeatured = $request->has('is_featured') && $request->is_featured;
            }
            
            News::create([
                'category_id' => $request->category_id,
                'title' => $request->title,
                'slug' => $slug,
                'content' => $request->content,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'image_path' => $imagePath,
                'status' => $request->status,
                'published_at' => $publishedAt,
                'is_featured' => $isFeatured,
            ]);

            return redirect()->route('news.index')
                           ->with('success', 'News created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create news: ' . $e->getMessage());
        }
    }

    public function edit(News $news)
    {
        $categories = NewsCategory::active()->ordered()->get();
        return view('pages.news.edit', compact('news', 'categories'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'category_id' => 'required|exists:news_categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'published_at' => 'nullable|date',
            'status' => 'required|in:draft,published',
            'is_featured' => 'nullable|boolean',
        ], [
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Selected category is invalid',
            'title.required' => 'Title is required',
            'title.unique' => 'Title already exists',
            'title.max' => 'Title cannot exceed 255 characters',
            'content.required' => 'Content is required',
            'meta_title.max' => 'Meta title cannot exceed 255 characters',
            'meta_description.max' => 'Meta description cannot exceed 500 characters',
            'meta_keywords.max' => 'Meta keywords cannot exceed 255 characters',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 5MB',
            'published_at.date' => 'Published date must be a valid date',
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',
        ]);

        try {
            // Check if selected category is active
            $category = NewsCategory::findOrFail($request->category_id);
            if (!$category->is_active) {
                return redirect()->back()
                               ->withInput()
                               ->withErrors(['category_id' => 'Selected category is not active.']);
            }

            // Generate slug if title changed
            $slug = GeneratorService::generateSlug(new News(), $request->title, $news->id);

            // Update image if new file provided
            $imagePath = ImageService::updateImage(
                $request->file('image'),
                $news->image_path,
                'news',
                85,
                1200
            );

            if ($request->has('is_featured') && $request->is_featured && $request->status !== 'published') {
                return redirect()->back()
                               ->withInput()
                               ->withErrors(['is_featured' => 'News must be published to be featured on home page.']);
            }
            
            if ($request->has('is_featured') && $request->is_featured && $request->status === 'published') {
                News::where('is_featured', true)
                    ->where('id', '!=', $news->id)
                    ->update(['is_featured' => false]);
            }
            
            $publishedAt = $news->published_at;
            $isFeatured = false;
            
            if ($request->status === 'published') {
                if (!$publishedAt || $request->published_at) {
                    $publishedAt = $request->published_at ? $request->published_at : now();
                }
                $isFeatured = $request->has('is_featured') && $request->is_featured;
            } else {
                $publishedAt = null;
                $isFeatured = false;
            }
            
            $news->update([
                'category_id' => $request->category_id,
                'title' => $request->title,
                'slug' => $slug,
                'content' => $request->content,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'image_path' => $imagePath,
                'status' => $request->status,
                'published_at' => $publishedAt,
                'is_featured' => $isFeatured,
            ]);

            return redirect()->back()
                           ->with('success', 'News updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update news: ' . $e->getMessage());
        }
    }

    public function destroy(News $news)
    {
        try {
            // Delete image if exists
            if ($news->image_path) {
                ImageService::deleteFile($news->image_path);
            }

            $news->delete();

            return redirect()->route('news.index')
                           ->with('success', 'News deleted successfully');

        } catch (\Exception $e) {
            return redirect()->route('news.index')
                           ->with('error', 'Failed to delete news: ' . $e->getMessage());
        }
    }
}