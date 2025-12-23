<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     */
    public function switch(Request $request, $locale)
    {
        // Validate locale
        if (!in_array($locale, array_keys(config('app.available_locales')))) {
            abort(404);
        }

        // Set both session and cookie
        Session::put('locale', $locale);
        App::setLocale($locale);
        

        // dd($locale);
        // Create response with redirect
        $response = redirect()->back()->with('success', 'Language updated successfully');
        
        // Set cookie for 1 year
        $response->withCookie(cookie('locale', $locale, 525600));

        return $response;
    }
}
