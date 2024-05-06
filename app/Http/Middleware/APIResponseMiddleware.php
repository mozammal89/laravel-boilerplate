<?php

namespace App\Http\Middleware;

use App\Providers\ValidationServiceProvider;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class APIResponseMiddleware
 * Middleware for formatting API responses.
 */
class APIResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the response from the next middleware in the pipeline
        $response = $next($request);

        // If the response is not a JsonResponse instance
        if (!$response instanceof JsonResponse) {
            return $response;
        }

        // Prepare the formatted response structure
        $formattedResponse = [
            'code' => $response->getStatusCode(),
            'message' => '',
            'data' => [],
            'errors' => [],
        ];

        // Check if the response has any content
        if ($response->getContent()) {
            // Merge the original response data into the formatted response
            $responseData = json_decode($response->getContent(), true);
            $formattedResponse['data'] = $responseData['data'] ?? [];
            $formattedResponse['message'] = $responseData['message'] ?? '';
            $formattedResponse['errors'] = $responseData['errors'] ?? [];

            if (count($formattedResponse['errors']) > 0) {
                $validation_message = ValidationServiceProvider::getValidationMessage($formattedResponse['errors']);
                if ($validation_message != null) {
                    $formattedResponse['message'] = $validation_message;
                }
            }
        }

        // Set the formatted response content
        $response->setContent(json_encode($formattedResponse));

        return $response;
    }
}
