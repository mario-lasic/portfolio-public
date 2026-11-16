<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Support\Seo;
use App\Http\Controllers\ProjectController;

Route::get('/', function () {
    return Inertia::render("Home", [
        'seo' => Seo::forPage(
            'Mario Lasić | Junior Backend Developer',
            'Junior backend developer working with PHP, Laravel and MySQL. Explore my projects, skills and background, or get in touch to request my CV.',
            '/',
        ),
    ]);
});
Route::get('/projects', [ProjectController::class, "index"]);
Route::get('/projects/{slug}', [ProjectController::class, "show"]);
Route::post('/contact', [ContactController::class, "store"]);

Route::get('/sitemap.xml', function () {
    $urls = array_map(fn (string $path) => Seo::url($path), Seo::publicPaths());

    return response()->view('seo.sitemap', ['urls' => $urls])
        ->header('Content-Type', 'application/xml; charset=UTF-8');
});

Route::get('/robots.txt', function () {
    return response("User-agent: *\nDisallow:\n\nSitemap: " . Seo::url('/sitemap.xml') . "\n")
        ->header('Content-Type', 'text/plain; charset=UTF-8');
});
