<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use App\Traits\HasPermissionChecks;
class PasswordController extends Controller
{
    use HasPermissionChecks;
    /**
     * Show the user's password settings page.
     */
    public function edit(): Response
    {

        $this->authorizeAction('settings.view');
        return Inertia::render('settings/Password');
    }

    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $this->authorizeAction('settings.edit');
        $validated = $request->validate([
    'current_password' => ['required', 'current_password'],
    'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => __('setting.password.validate_error.current_password_required'),
            'current_password.current_password' => __('setting.password.validate_error.current_password_invalid'),

            'password.required' => __('setting.password.validate_error.password_required'),
            'password.confirmed' => __('setting.password.validate_error.password_confirmed'),
        ]);


        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back();
    }
}
