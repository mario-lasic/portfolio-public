<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'https://portfolio.example']);
        $this->withoutVite();
    }

    public function test_public_pages_include_metadata_in_initial_html(): void
    {
        foreach ([
            '/' => 'Mario Lasić | Junior Backend Developer',
            '/projects' => 'Projects | Mario Lasić',
            '/projects/gymlog' => 'GymLog | Mario Lasić',
        ] as $path => $title) {
            $response = $this->get($path);
            $response->assertOk();
            $response->assertSee('<title data-inertia="">' . $title . '</title>', false);
            $response->assertSee('<meta name="description" content="', false);
            $response->assertSee('href="https://portfolio.example' . $path . '" data-inertia="canonical"', false);
            $response->assertSee('property="og:url" content="https://portfolio.example' . $path . '"', false);
            $response->assertSee('property="og:image" content="https://portfolio.example/images/portfolio-share.png"', false);
            $this->assertSame(1, substr_count($response->getContent(), '<title '));
            $this->assertSame(1, substr_count($response->getContent(), '<meta name="description"'));
        }
    }

    public function test_inertia_navigation_receives_page_specific_metadata_without_query_string(): void
    {
        $version = $this->get('/')->viewData('page')['version'];

        $this->get('/projects/gymlog?source=test', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => $version,
        ])
            ->assertOk()
            ->assertJsonPath('component', 'ProjectDetails')
            ->assertJsonPath('props.seo.title', 'GymLog | Mario Lasić')
            ->assertJsonPath('props.seo.description', config('projects.0.summary'))
            ->assertJsonPath('props.seo.canonical', 'https://portfolio.example/projects/gymlog')
            ->assertJsonPath('props.seo.image', 'https://portfolio.example/images/portfolio-share.png');
    }

    public function test_sitemap_only_lists_real_public_pages(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);
        $urls = [];
        foreach ($xml->url as $entry) {
            $urls[] = (string) $entry->loc;
        }
        $this->assertSame([
            'https://portfolio.example/',
            'https://portfolio.example/projects',
            'https://portfolio.example/projects/gymlog',
        ], $urls);
    }

    public function test_robots_points_to_configured_sitemap(): void
    {
        $this->get('/robots.txt')->assertOk()
            ->assertSee('Sitemap: https://portfolio.example/sitemap.xml', false);
    }

    public function test_placeholder_and_unknown_projects_remain_not_found(): void
    {
        $this->get('/projects/coming-soon')->assertNotFound();
        $this->get('/projects/unknown-project')->assertNotFound();
    }
}
