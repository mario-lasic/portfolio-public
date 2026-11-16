<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        @fonts
        @vite('resources/js/app.js')
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        @isset($page['props']['seo'])
            @php($seo = $page['props']['seo'])
            <title data-inertia="">{{ $seo['title'] }}</title>
            <meta name="description" content="{{ $seo['description'] }}" data-inertia="description">
            <link rel="canonical" href="{{ $seo['canonical'] }}" data-inertia="canonical">
            <meta property="og:type" content="website" data-inertia="og:type">
            <meta property="og:site_name" content="Mario Lasić — Portfolio" data-inertia="og:site_name">
            <meta property="og:locale" content="en_US" data-inertia="og:locale">
            <meta property="og:title" content="{{ $seo['title'] }}" data-inertia="og:title">
            <meta property="og:description" content="{{ $seo['description'] }}" data-inertia="og:description">
            <meta property="og:url" content="{{ $seo['canonical'] }}" data-inertia="og:url">
            <meta property="og:image" content="{{ $seo['image'] }}" data-inertia="og:image">
            <meta property="og:image:width" content="1200" data-inertia="og:image:width">
            <meta property="og:image:height" content="630" data-inertia="og:image:height">
            <meta property="og:image:alt" content="Mario Lasić — Junior Backend Developer, PHP &amp; Laravel" data-inertia="og:image:alt">
            <meta name="twitter:card" content="summary_large_image" data-inertia="twitter:card">
            <meta name="twitter:title" content="{{ $seo['title'] }}" data-inertia="twitter:title">
            <meta name="twitter:description" content="{{ $seo['description'] }}" data-inertia="twitter:description">
            <meta name="twitter:image" content="{{ $seo['image'] }}" data-inertia="twitter:image">
            <meta name="twitter:image:alt" content="Mario Lasić — Junior Backend Developer, PHP &amp; Laravel" data-inertia="twitter:image:alt">
        @endisset
        <x-inertia::head/>
    </head>
    <body>
        <x-inertia::app/>
    </body>
</html>
