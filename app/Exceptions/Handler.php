<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
		\Illuminate\Auth\AuthenticationException::class,
		\Illuminate\Auth\Access\AuthorizationException::class,
		\Symfony\Component\HttpKernel\Exception\HttpException::class,
		\Illuminate\Database\Eloquent\ModelNotFoundException::class,
		\Illuminate\Session\TokenMismatchException::class,
		\Illuminate\Validation\ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
		if ($exception instanceof MethodNotAllowedHttpException)
		{
			//return Redirect::to('/exception')->with('error', config('constants.exception'));
		}
		/* if ($this->isHttpException($exception))
		{
			if ($exception->getStatusCode() == 404)
				{
					return response()->view('errors.' . '404', [], 404);
				}
		} */
        $response = parent::render($request, $exception);

        return $this->applyConfiguredCorsHeaders($request, $response);
    }

    /**
     * Ensure API / Sanctum CORS paths get the same headers as successful responses when
     * an exception short-circuits before the normal CORS middleware finishes (fixes browser
     * "CORS error" on GET when OPTIONS preflight already succeeded).
     */
    protected function applyConfiguredCorsHeaders(Request $request, Response $response): Response
    {
        if (! $this->requestMatchesCorsPaths($request)) {
            return $response;
        }

        $origin = $request->headers->get('Origin');
        if (! is_string($origin) || $origin === '' || ! $this->corsOriginIsAllowed($origin)) {
            return $response;
        }

        if ($response->headers->has('Access-Control-Allow-Origin')) {
            return $response;
        }

        $response->headers->set('Access-Control-Allow-Origin', $origin);

        if (config('cors.supports_credentials')) {
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }

        $vary = array_values(array_filter(array_map('trim', preg_split('/\s*,\s*/', (string) $response->headers->get('Vary') ?: '', -1, PREG_SPLIT_NO_EMPTY))));
        if (! in_array('Origin', $vary, true)) {
            $vary[] = 'Origin';
            $response->headers->set('Vary', implode(', ', $vary));
        }

        return $response;
    }

    protected function requestMatchesCorsPaths(Request $request): bool
    {
        foreach (config('cors.paths', []) as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }

    protected function corsOriginIsAllowed(string $origin): bool
    {
        foreach (config('cors.allowed_origins', []) as $allowed) {
            if ($allowed === $origin) {
                return true;
            }
        }

        foreach (config('cors.allowed_origins_patterns', []) as $pattern) {
            if ($pattern !== '' && Str::is($pattern, $origin)) {
                return true;
            }
        }

        return false;
    }

	protected function unauthenticated($request, AuthenticationException $exception)
	{
		// Check if this is an API request
		// API routes should always return JSON 401, not HTML redirects
		$isApiRoute = $request->is('api/*');
		
		// Also check if Authorization header with Bearer token is present
		// This indicates an API authentication attempt
		$hasBearerToken = $request->hasHeader('Authorization') && 
						  str_starts_with($request->header('Authorization', ''), 'Bearer ');
		
		// Return JSON 401 for API routes or requests with bearer tokens
		if ($request->expectsJson() || $isApiRoute || $hasBearerToken)
		{
			$response = response()->json([
				'success' => false,
				'message' => 'Unauthenticated.',
				'error' => 'Invalid or expired authentication token.'
			], 401);

			return $this->applyConfiguredCorsHeaders($request, $response);
		}
		
		// For web routes, redirect to login page
		$guard = Arr::get($exception->guards(), 0);

		switch ($guard)
		{
			case 'admin': $login = 'crm.login'; // Updated from admin.login
			break;
			default: $login = 'crm.login';
			break;
		}
        return redirect()->guest(route($login));
	}
}
