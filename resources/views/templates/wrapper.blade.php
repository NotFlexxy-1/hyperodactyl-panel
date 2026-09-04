<!DOCTYPE html>
<html>
    <head>
        <title>{{ $branding['site_name'] ?? config('app.name', 'Hyperodactyl') }}</title>

        @section('meta')
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <meta name="robots" content="noindex">
            <meta name="description" content="{{ $branding['social_description'] ?? '' }}">
            <meta name="keywords" content="{{ $branding['meta_keywords'] ?? '' }}">

            <meta property="og:title" content="{{ $branding['site_name'] ?? config('app.name', 'Hyperodactyl') }}">
            <meta property="og:description" content="{{ $branding['social_description'] ?? '' }}">
            @if(!empty($branding['og_image_url']))
                <meta property="og:image" content="{{ $branding['og_image_url'] }}">
            @endif
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="{{ $branding['site_name'] ?? config('app.name', 'Hyperodactyl') }}">
            <meta name="twitter:description" content="{{ $branding['social_description'] ?? '' }}">
            @if(!empty($branding['og_image_url']))
                <meta name="twitter:image" content="{{ $branding['og_image_url'] }}">
            @endif

            <link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png">
            @if(!empty($branding['favicon_url']))
                <link rel="icon" href="{{ $branding['favicon_url'] }}">
                <link rel="shortcut icon" href="{{ $branding['favicon_url'] }}">
            @else
                <link rel="icon" type="image/png" href="/favicons/favicon-32x32.png" sizes="32x32">
                <link rel="icon" type="image/png" href="/favicons/favicon-16x16.png" sizes="16x16">
                <link rel="shortcut icon" href="/favicons/favicon.ico">
            @endif
            <link rel="manifest" href="/favicons/manifest.json">
            <link rel="mask-icon" href="/favicons/safari-pinned-tab.svg" color="#5b8cff">
            <meta name="msapplication-config" content="/favicons/browserconfig.xml">
            <meta name="theme-color" content="{{ $branding['color_primary'] ?? '#0e4688' }}">

            <style>{!! $brandingCssVariables ?? '' !!}</style>
        @show

        @section('user-data')
            @if(!is_null(Auth::user()))
                <script>
                    window.HyperodactylUser = {!! json_encode(Auth::user()->toVueObject()) !!};
                </script>
            @endif
            @if(!empty($siteConfiguration))
                <script>
                    window.SiteConfiguration = {!! json_encode($siteConfiguration) !!};
                </script>
            @endif
        @show

        @yield('assets')

        @include('layouts.scripts')
    </head>
    <body class="{{ $css['body'] ?? 'bg-neutral-50' }}">
        @section('content')
            @yield('above-container')
            @yield('container')
            @yield('below-container')
        @show
        @section('scripts')
            {!! $asset->js('main.js') !!}
        @show
    </body>
</html>
