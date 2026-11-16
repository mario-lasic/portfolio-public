<?php

namespace App\Support;

class Seo
{
    public static function url(string $path = '/'): string
    {
        return rtrim(config('app.url'), '/') . ($path === '/' ? '/' : '/' . ltrim($path, '/'));
    }

    public static function forPage(string $title, string $description, string $path): array
    {
        return [
            'title' => $title,
            'description' => $description,
            'canonical' => self::url($path),
            'image' => self::url('/images/portfolio-share.png'),
        ];
    }

    public static function publicPaths(): array
    {
        $paths = ['/', '/projects'];

        foreach (config('projects', []) as $project) {
            if (! ($project['is_placeholder'] ?? false)) {
                $paths[] = '/projects/' . rawurlencode($project['slug']);
            }
        }

        return $paths;
    }
}
