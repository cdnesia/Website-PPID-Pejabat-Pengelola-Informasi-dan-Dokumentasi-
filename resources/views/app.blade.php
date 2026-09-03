<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $meta['title'] }}</title>
        <meta name="description" content="{{ $meta['description'] }}">
        <link rel="canonical" href="{{ $meta['url'] }}">

        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ $meta['title'] }}">
        <meta property="og:title" content="{{ $meta['title'] }}">
        <meta property="og:description" content="{{ $meta['description'] }}">
        <meta property="og:image" content="{{ $meta['image'] }}">
        <meta property="og:url" content="{{ $meta['url'] }}">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $meta['title'] }}">
        <meta name="twitter:description" content="{{ $meta['description'] }}">
        <meta name="twitter:image" content="{{ $meta['image'] }}">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-background text-foreground antialiased">
        <div id="app"></div>
    </body>
</html>
