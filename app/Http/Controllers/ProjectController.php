<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Support\Seo;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = config('projects');
        return Inertia::render('Projects', [
            'projects' => $projects,
            'seo' => Seo::forPage(
                'Projects | Mario Lasić',
                'Explore Mario Lasić’s practical backend projects, including descriptions, screenshots, technologies and source code.',
                '/projects',
            ),
        ]);
    }

    /**
     * @throws FileNotFoundException
     */
    public function show(string $slug)
    {
        $projects = config('projects');
        foreach ($projects as $project) {
            if ($project['slug'] === $slug) {
                if (isset($project['is_placeholder']) && $project['is_placeholder'] === true) {
                    abort(404);
                }
                $path = resource_path($project['content_file']);
                $contentHtml = Str::markdown(File::get($path));

                return Inertia::render('ProjectDetails', [
                    'project' => $project,
                    'contentHtml' => $contentHtml,
                    'seo' => Seo::forPage(
                        $project['title'] . ' | Mario Lasić',
                        $project['summary'],
                        '/projects/' . rawurlencode($project['slug']),
                    ),
                ]);
            }
        }
        abort(404);
    }
}
