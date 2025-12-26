<?php

namespace App\Modules\Document\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\Document\Http\Requests\CodeVerifyRequest;
use App\Modules\Document\Http\Requests\DocumentFileRequest;
use App\Modules\Document\Http\Requests\SendDocumentRequest;
use App\Modules\Document\Models\Document;
use App\Modules\Document\Services\DocumentDataService;
use Illuminate\Http\JsonResponse;
use App\Modules\Document\Http\Requests\SendRejectDocumentRequest;
use App\Modules\Document\Http\Requests\SendSignDocumentRequest;


class DocumentDataController extends BaseController
{
    protected DocumentDataService $service;

    public function __construct(DocumentDataService $service)
    {
        $this->service = $service;
    }

    public function getDocumentFileData(string $documentId, DocumentFileRequest $request): JsonResponse
    {
        $attributes = $request->validated();
        $attributes['uid'] = $documentId;
        $response = $this->service->getDocumentFileData($attributes);

        if (is_array($response) && array_key_exists('errors', $response)) {
            return $this->sendErrorResponse($response);
        }

        return $this->sendResponse($response);
    }

    public function getRequestFileData(string $requestId): JsonResponse
    {
        $attributes['uid'] = $requestId;
        $response = $this->service->getRequestFileData($attributes);

        if (is_array($response) && array_key_exists('errors', $response)) {
            return $this->sendErrorResponse($response);
        }

        return $this->sendResponse($response);
    }

    public function signDocument(string $documentId, SendSignDocumentRequest $request): JsonResponse
    {
        $attributes = $request->validated();
        $attributes['uid'] = $documentId;
        $response = $this->service->signDocument($attributes);

        if (is_array($response) && array_key_exists('errors', $response)) {
            return $this->sendErrorResponse($response);
        }

        return $this->sendResponse($response);
    }

    public function signRequest(string $requestId, SendSignDocumentRequest $request): JsonResponse
    {
        $attributes = $request->validated();
        $attributes['uid'] = $requestId;
        $response = $this->service->signRequest($attributes);

        if (is_array($response) && array_key_exists('errors', $response)) {
            return $this->sendErrorResponse($response);
        }

        return $this->sendResponse($response);
    }

    public function confirmRequest(string $requestId): JsonResponse
    {
        $attributes['id'] = $requestId;
        $type = 'send_doc';
        $response = $this->service->confirmRequest($type, $attributes);

        if (is_array($response) && array_key_exists('errors', $response)) {
            return $this->sendErrorResponse($response);
        }

        return $this->sendResponse($response);
    }

    public function rejectRequest(string $requestId, SendRejectDocumentRequest $request): JsonResponse
    {
        $attributes = $request->validated();
        $attributes['uid'] = $requestId;
        $response = $this->service->rejectRequest($attributes);

        if (is_array($response) && array_key_exists('errors', $response)) {
            return $this->sendErrorResponse($response);
        }

        return $this->sendResponse($response);
    }

    public function sendCode(): JsonResponse
    {
        $response = $this->service->sendCode();

        if (!$response) {
            return $this->sendErrorResponse([
                'errors' => [
                    'sms' => 'Ошибка отправки кода'
                ]
            ]);
        }

        if (is_array($response) && array_key_exists('errors', $response)) {
            return $this->sendErrorResponse($response);
        }

        return $this->sendResponse($response);
    }

    public function getNewEdsFileData(): JsonResponse
    {
        $response = $this->service->getNewEdsFileData();

        if (is_array($response) && array_key_exists('errors', $response)) {
            return $this->sendErrorResponse($response);
        }

        return $this->sendResponse($response);
    }

    public function sendNewEds(CodeVerifyRequest $request): JsonResponse
    {
        $code = $request->validated()['code'];
        $response = $this->service->sendNewEds($code);

        if (is_array($response) && array_key_exists('errors', $response)) {
            return $this->sendErrorResponse($response);
        }

        return $this->sendResponse($response);
    }

    public function getRequestOptions(Document $requestType): JsonResponse
    {
        $options = $this->service->getRequestOptions($requestType->type);

        if (array_key_exists('errors', $options)) {
            return $this->sendErrorResponse($options);
        }

        return $this->sendResponse($options);
    }

    public function sendRequest(SendDocumentRequest $request, Document $requestType): JsonResponse
    {
        $attributes = $request->validated();
        $response = $this->service->sendRequest($requestType->type, $attributes);

        if (is_array($response) && array_key_exists('errors', $response)) {
            return $this->sendErrorResponse($response);
        }

        return $this->sendResponse($response);
    }
}
