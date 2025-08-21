<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Gallery;
use App\Models\StaffMember;
use App\Models\Equipment;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    /**
     * Show the landing page with real data
     */
    public function landing(): View
    {
        // Get featured article first (prioritize featured, then latest)
        $featured = Article::published()->featured()
            ->orderByDesc('published_at')->first() 
            ?? Article::published()->latest('published_at')->first();

        // Get other latest articles for homepage (exclude featured)
        $articles = Article::published()
            ->when($featured, fn($q) => $q->where('id', '!=', $featured->id))
            ->latest('published_at')
            ->take(3)
            ->select('id', 'title', 'slug', 'excerpt', 'featured_image_path', 'published_at', 'category')
            ->get();

        // Get gallery items for homepage using featured slots system
        $galleryItems = collect();
        
        // Try to get featured slots from site settings
        $featuredSetting = SiteSetting::where('key', 'homepage_gallery_slots')->first();
        
        if ($featuredSetting && $featuredSetting->content) {
            $slots = json_decode($featuredSetting->content, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($slots)) {
                // Get gallery items for each configured slot (1-4)
                for ($i = 1; $i <= 4; $i++) {
                    if (isset($slots[$i]) && $slots[$i]) {
                        $gallery = Gallery::active()
                            ->where('id', $slots[$i])
                            ->select('id', 'title', 'image_path', 'description', 'category')
                            ->first();
                        
                        if ($gallery) {
                            $galleryItems->push($gallery);
                        }
                    }
                }
            }
        }
        
        // Fallback: if no featured slots configured or insufficient items, fill with sort_order
        if ($galleryItems->count() < 4) {
            $excludeIds = $galleryItems->pluck('id')->toArray();
            
            $fallbackItems = Gallery::active()
                ->when(!empty($excludeIds), function($query) use ($excludeIds) {
                    return $query->whereNotIn('id', $excludeIds);
                })
                ->orderBy('sort_order')
                ->take(4 - $galleryItems->count())
                ->select('id', 'title', 'image_path', 'description', 'category')
                ->get();
                
            $galleryItems = $galleryItems->concat($fallbackItems);
        }
        
        // Ensure we have exactly 4 items or less
        $galleryItems = $galleryItems->take(4);

        // Get site settings and lab configuration
        $siteSettings = SiteSetting::all()->pluck('value', 'key');
        $labConfig = config('lab');

        return view('public.landing', compact('featured', 'articles', 'galleryItems', 'siteSettings', 'labConfig'));
    }

    /**
     * Show the articles page
     */
    public function articles(Request $request): View
    {
        // Get featured article first (prioritize featured, then latest) - always show regardless of filter
        $featured = Article::published()->featured()
            ->orderByDesc('published_at')->first() 
            ?? Article::published()->latest('published_at')->first();

        // Get other articles - do NOT exclude featured article from filtered results
        $query = Article::published()->with(['publisher'])
            ->latest('published_at');

        // Apply filters
        if ($request->filled('category')) {
            $query->byCategory($request->get('category'));
        }

        if ($request->filled('search')) {
            $query->search($request->get('search'));
        }

        $others = $query->paginate(6)->withQueryString();
        $categories = Article::getCategories();
        $currentCategory = $request->get('category');

        return view('public.artikel', compact('featured', 'others', 'categories', 'currentCategory'));
    }

    /**
     * Show specific article
     */
    public function showArticle(Article $article): View
    {
        // Check if article is published
        if (!$article->is_published || !$article->published_at || $article->published_at > now()) {
            abort(404);
        }

        // Increment view count
        $article->incrementViews();

        // Get related articles
        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->take(3)
            ->get();

        return view('public.artikel-detail', compact('article', 'relatedArticles'));
    }

    /**
     * Show the staff page
     */
    public function staff(Request $request): View
    {
        $query = StaffMember::active()->ordered();

        // Apply staff type filter if provided
        if ($request->filled('type') && in_array($request->get('type'), \App\Enums\StaffType::values())) {
            $staffType = \App\Enums\StaffType::from($request->get('type'));
            $query->type($staffType);
        }

        $staffMembers = $query->paginate(12)->withQueryString();
        $staffTypes = \App\Enums\StaffType::options();
        $currentType = $request->get('type');

        return view('public.staff', compact('staffMembers', 'staffTypes', 'currentType'));
    }

    /**
     * Show the equipment catalog page
     */
    public function equipmentCatalog(Request $request): View
    {
        $query = Equipment::with('category')->active();

        // Apply filters
        if ($request->filled('category_id')) {
            $query->byCategory($request->get('category_id'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('manufacturer', 'like', "%{$search}%");
            });
        }

        if ($request->filled('available_only') && $request->get('available_only') === '1') {
            $query->available();
        }

        $equipment = $query->orderBy('name')->paginate(12);
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('public.layanan.katalog-alat', compact('equipment', 'categories'));
    }

    /**
     * Show the borrow form page
     */
    public function borrowForm(): View
    {
        $equipment = Equipment::active()->available()->with('category')->orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('public.layanan.form-peminjaman', compact('equipment', 'categories'));
    }

    /**
     * Show the visit form page
     */
    public function visitForm(): View
    {
        $labConfig = config('lab');

        return view('public.layanan.kunjungan', compact('labConfig'));
    }

    /**
     * Show the testing service page
     */
    public function testingService(): View
    {
        $labConfig = config('lab');

        return view('public.layanan.pengujian', compact('labConfig'));
    }

    /**
     * Show the gallery page
     */
    public function gallery(Request $request): View
    {
        $query = Gallery::published()->ordered();

        // Apply filters
        if ($request->filled('category')) {
            $query->byCategory($request->get('category'));
        }

        if ($request->filled('q')) {
            $query->search($request->get('q'));
        }

        $galleryItems = $query->paginate(12)->withQueryString();
        $categories = Gallery::getCategories();
        $currentCategory = $request->get('category');
        $currentSearch = $request->get('q');

        // Calculate statistics
        $totalPhotos = Gallery::published()->count();
        $distinctCategories = Gallery::published()->select('category')->distinct()->pluck('category')->filter();
        $totalCategories = $distinctCategories->count();
        
        $categoryStats = $distinctCategories->map(function ($category) {
            return [
                'slug' => $category,
                'name' => Gallery::getCategories()[$category] ?? $category,
                'count' => Gallery::published()->byCategory($category)->count()
            ];
        })->sortByDesc('count');

        return view('public.galeri', compact(
            'galleryItems', 
            'categories', 
            'currentCategory', 
            'currentSearch',
            'totalPhotos', 
            'totalCategories', 
            'categoryStats'
        ));
    }
}
