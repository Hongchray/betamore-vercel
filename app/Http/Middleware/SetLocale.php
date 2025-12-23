<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getLocale($request);
        
        if (in_array($locale, array_keys(config('app.available_locales')))) {
            App::setLocale($locale);
            
            // Set font configuration for the current locale
            $this->setLocaleFont($locale);
        }

        return $next($request);
    }

    private function getLocale(Request $request): string
    {
        // 1. Check session first
        if (Session::has('locale')) {
            return Session::get('locale');
        }

        // 2. Check cookie
        if ($request->hasCookie('locale')) {
            return $request->cookie('locale');
        }

        // 3. Check browser preference
        $browserLocale = $request->getPreferredLanguage(array_keys(config('app.available_locales')));
        if ($browserLocale) {
            return $browserLocale;
        }

        // 4. Fall back to default
        return config('app.locale');
    }

    private function setLocaleFont(string $locale): void
    {
        $fontConfig = config('fonts.locale_fonts.' . $locale);
        
        if ($fontConfig) {
            // Store font configuration in session or view data
            Session::put('locale_font', $fontConfig);
        }
    }
}
