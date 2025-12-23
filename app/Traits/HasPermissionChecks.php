<?php

namespace App\Traits;

use Illuminate\Http\Response;
use Inertia\Inertia;

trait HasPermissionChecks
{
    /**
     * Authorize admin action with permission
     */
    protected function authorizeAction(string $permission): void
    {
        if (!auth()->check()) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        if (!auth()->user()->can($permission)) {
            $message =  __(('permissions.default'));
            $submessage = __(('permissions.submessage'));

            // Create a simple page that shows alert and redirects back
            Inertia::render('Errors/PermissionAlert', [
                'message' => $message,
                'submessage' => $submessage
            ])->toResponse(request())->send();
            exit;
        }
    }
}
