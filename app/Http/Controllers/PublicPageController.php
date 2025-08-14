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
        // Get latest articles for homepage
        $articles = Article::published()
            ->latest()
            ->take(3)
            ->select('id', 'title', 'slug', 'excerpt', 'featured_image_path', 'published_at', 'category')
            ->get();

        // Get gallery items for homepage (reduced to 4 items)
        $galleryItems = Gallery::orderBy('sort_order')
            ->take(4)
            ->select('id', 'title', 'image_path', 'description', 'category')
            ->get();

        // Get site settings and lab configuration
        $siteSettings = SiteSetting::all()->pluck('value', 'key');
        $labConfig = config('lab');

        return view('public.landing', compact('articles', 'galleryItems', 'siteSettings', 'labConfig'));
    }

    /**
     * Show the articles page
     */
    public function articles(Request $request): View
    {
        $query = Article::published()->with(['publisher'])->latest();

        // Apply filters
        if ($request->filled('category')) {
            $query->byCategory($request->get('category'));
        }

        if ($request->filled('search')) {
            $query->search($request->get('search'));
        }

        $articles = $query->paginate(9);
        $categories = Article::getCategories();

        return view('public.artikel', compact('articles', 'categories'));
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
    public function staff(): View
    {
        $staffMembers = StaffMember::active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('public.staff', compact('staffMembers'));
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
        $query = Gallery::orderBy('sort_order');

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        $galleryItems = $query->paginate(12);
        $categories = Gallery::distinct()->pluck('category')->filter();

        return view('public.galeri', compact('galleryItems', 'categories'));
    }
}
