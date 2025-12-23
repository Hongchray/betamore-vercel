<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class HandleAuthorizationException
{
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (AuthorizationException $e) {
            if ($request->header('X-Inertia')) {
                return Inertia::render('Errors/403')
                    ->toResponse($request)
                    ->setStatusCode(Response::HTTP_FORBIDDEN);
            }

            return response()->view('errors.403', [], Response::HTTP_FORBIDDEN);
        }
    }
}
