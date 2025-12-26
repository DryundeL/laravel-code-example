<?php

namespace App\Http\Middleware;

use App\Traits\KeysCaseConverter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TransformCaseMiddleware
{
    use KeysCaseConverter;

    /**
     * Handle an incoming request.
     *
     * Transforms incoming request data to snake_case and outgoing response data to camelCase.
     *
     * @param Request $request
     * @param Closure(Request): Response $next
     * @return Response
     * @throws \JsonException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        if ($request->isMethod('post') && str_contains($request->header('Content-Type', ''), 'multipart/form-data')) {
            $nonFileInput = $request->except($request->files->keys());
            $snakeCasedNonFileInput = $this->convertKeysToSnakeCase($nonFileInput);
            $request->request->replace($snakeCasedNonFileInput);

            $files = $request->files->all();
            $snakeCasedFiles = [];

            foreach ($files as $key => $file) {
                $snakeKey = $this->toSnakeCase($key);
                $snakeCasedFiles[$snakeKey] = $file;
            }

            $request->files->replace($snakeCasedFiles);
        } else {
            $snakeCasedInput = $this->convertKeysToSnakeCase($input);
            $request->replace($snakeCasedInput);
        }

        $response = $next($request);

        if ($this->isJsonResponse($response)) {
            $originalContent = $response->getContent();
            $data = json_decode($originalContent, false, 512, JSON_THROW_ON_ERROR);

            if (json_last_error() === JSON_ERROR_NONE && (is_array($data) || is_object($data))) {
                $camelCasedData = $this->convertKeysToCamelCase($data);
                $response->setContent(json_encode($camelCasedData, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
            }
        }

        return $response;
    }

    /**
     * Check if the response is a JSON response.
     *
     * @param Response $response
     * @return bool
     */
    protected function isJsonResponse(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type');
        return $contentType && str_contains($contentType, 'application/json');
    }
}
