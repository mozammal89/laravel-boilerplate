<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Sentry\Laravel\Integration;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (App::environment() == "production") {
                Integration::captureUnhandledException($e);
            }
        });
    }

    function render($request, Throwable $e): Response|JsonResponse|RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        if ($this->isHttpException($e)) {
            if ($e->getStatusCode() == 404) {
                return response()->view('errors.404', [], 404);
            }
            if ($e->getStatusCode() == 500) {
                return response()->view('errors.500', [], 500);
            }
        }

        if ($request->is('api*')) {
            $request->headers->set('Accept', 'application/json');
        }

        return parent::render($request, $e);
    }
}
