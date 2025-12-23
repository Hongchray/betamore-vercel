<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      @class(['dark' => ($appearance ?? 'system') == 'dark'])
      style="--font-family: {{ config('fonts.locale_fonts.' . app()->getLocale() . '.css_var', 'Roboto, sans-serif') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $siteInfo = \App\Models\SiteInfo::first();
            $siteName = $siteInfo?->site_name ?? config('app.name', 'Laravel');
            $faviconUrl = $siteInfo?->favicon_url ?? '/favicon.ico';
            $metaDescription = $siteInfo?->meta_description ?? 'Laravel Application';
        @endphp

        {{-- Use dynamic site info --}}
        <title inertia>{{ $siteName }}</title>
        <meta name="description" content="{{ $metaDescription }}">
        
        <link rel="icon" href="{{ $faviconUrl }}" sizes="any">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color and font based on locale --}}
        <style>
            html {
                background-color: oklch(1 0 0);
                font-family: var(--font-family);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }

            /* Locale-specific font classes */
            .font-locale-en { font-family: 'Inter', sans-serif; }
            .font-locale-km { font-family: 'Kantumruy Pro', 'Khmer OS', sans-serif; }
            .font-locale-zh { font-family: 'Noto Sans SC', 'PingFang SC', sans-serif; }
        </style>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        
        {{-- Load fonts based on available locales --}}
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Kantumruy+Pro:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
        @routes
        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased font-locale-{{ app()->getLocale() }}">
        @inertia
    </body>
</html>
