<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Throwable;

class Handler extends ExceptionHandler
{
    // ... existing code ...

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle authorization exceptions with session flash
        if ($e instanceof AuthorizationException && session()->has('permission_error')) {
            return back()->with('permission_error', session('permission_error'));
        }

        return parent::render($request, $e);
    }
}
