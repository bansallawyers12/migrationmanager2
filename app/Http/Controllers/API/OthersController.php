<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Exception;

class OthersController extends Controller
{
    /**
     * Get Blog List
     * GET /api/blogs/list
     */
    public function getBlogList(Request $request)
    {
        try {
            $baseUrl = config('services.bansal_api.url', 'https://www.bansalimmigration.com.au/api/crm');
            $apiToken = config('services.bansal_api.token');
            $timeout = config('services.bansal_api.timeout', 30);

            if (empty($apiToken)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bansal API token not configured. Set BANSAL_API_TOKEN in .env'
                ], 500);
            }

            // Get query parameters from request
            $page = $request->get('page', 1);
            $searchQuery = $request->get('q');
            $featured = $request->get('featured');

            // Build query parameters array
            $queryParams = [
                'page' => $page
            ];

            // Add optional parameters if provided
            if ($searchQuery !== null && $searchQuery !== '') {
                $queryParams['q'] = $searchQuery;
            }

            if ($featured !== null && $featured !== '') {
                $queryParams['featured'] = $featured;
            }

            // Make API call to Bansal API
            $response = Http::timeout($timeout)
                ->withToken($apiToken)
                ->acceptJson()
                ->get("{$baseUrl}/blogs/list", $queryParams);

            if ($response->failed()) {
                Log::error('Bansal API Blog List Error', [
                    'method' => 'getBlogList',
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'query_params' => $queryParams
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch blog list from external API',
                    'error' => $response->status() === 404 ? 'Blog list not found' : 'API request failed'
                ], $response->status());
            }

            $data = $response->json();

            // Return the response as-is from the external API
            return response()->json($data, $response->status());

        } catch (RequestException $e) {
            $response = $e->response;
            $responseBody = $response?->json();
            $message = null;

            if (is_array($responseBody)) {
                $message = $responseBody['message']
                    ?? ($responseBody['error']['message'] ?? null);
            }

            $message = $message ?: $response?->body() ?: $e->getMessage();

            Log::error('Bansal API Blog List Request Error', [
                'method' => 'getBlogList',
                'status' => $response?->status(),
                'body' => $response?->body(),
                'error' => $message,
                'query_params' => [
                    'page' => $request->get('page', 1),
                    'q' => $request->get('q'),
                    'featured' => $request->get('featured')
                ]
            ]);

            return response()->json([
                'success' => false,
                'message' => $message ?: 'Failed to fetch blog list',
                'error' => 'API request failed'
            ], $response?->status() ?: 500);

        } catch (Exception $e) {
            Log::error('Bansal API Blog List Error', [
                'method' => 'getBlogList',
                'error_type' => get_class($e),
                'error' => $e->getMessage(),
                'query_params' => [
                    'page' => $request->get('page', 1),
                    'q' => $request->get('q'),
                    'featured' => $request->get('featured')
                ]
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while fetching blog list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Blog Detail
     * GET /api/blogs/detail/{id}
     */
    public function getBlogDetail(Request $request, $id)
    {
        try {
            $baseUrl = config('services.bansal_api.url', 'https://www.bansalimmigration.com.au/api/crm');
            $apiToken = config('services.bansal_api.token');
            $timeout = config('services.bansal_api.timeout', 30);

            if (empty($apiToken)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bansal API token not configured. Set BANSAL_API_TOKEN in .env'
                ], 500);
            }

            // Validate ID
            if (empty($id) || !is_numeric($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid blog ID provided'
                ], 400);
            }

            // Make API call to Bansal API
            $response = Http::timeout($timeout)
                ->withToken($apiToken)
                ->acceptJson()
                ->get("{$baseUrl}/blogs/detail/{$id}");

            if ($response->failed()) {
                Log::error('Bansal API Blog Detail Error', [
                    'method' => 'getBlogDetail',
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'id' => $id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch blog detail from external API',
                    'error' => $response->status() === 404 ? 'Blog not found' : 'API request failed'
                ], $response->status());
            }

            $data = $response->json();

            // Return the response as-is from the external API
            return response()->json($data, $response->status());

        } catch (RequestException $e) {
            $response = $e->response;
            $responseBody = $response?->json();
            $message = null;

            if (is_array($responseBody)) {
                $message = $responseBody['message']
                    ?? ($responseBody['error']['message'] ?? null);
            }

            $message = $message ?: $response?->body() ?: $e->getMessage();

            Log::error('Bansal API Blog Detail Request Error', [
                'method' => 'getBlogDetail',
                'status' => $response?->status(),
                'body' => $response?->body(),
                'error' => $message,
                'id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => $message ?: 'Failed to fetch blog detail',
                'error' => 'API request failed'
            ], $response?->status() ?: 500);

        } catch (Exception $e) {
            Log::error('Bansal API Blog Detail Error', [
                'method' => 'getBlogDetail',
                'error_type' => get_class($e),
                'error' => $e->getMessage(),
                'id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while fetching blog detail',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

