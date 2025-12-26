<?php

namespace App\Modules\Document\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\Document\Http\Requests\QrCodeRequest;
use App\Modules\Document\Models\Document;
use App\Modules\Document\Services\DocumentService;
use App\Modules\Document\Services\DocumentTemplateService;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Modules\Document\Http\Resources\RequestListCollection;
use App\Modules\Document\Http\Resources\RequestResource;


class DocumentController extends BaseController
{
    protected DocumentService $service;

    public function __construct(DocumentService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $documents = $this->service->getDocuments();

        if (array_key_exists('errors', $documents)) {
            return $this->sendErrorResponse($documents);
        }

        return $this->sendResponse($documents);
    }

    /**
     * @throws Exception
     */
    public function getRequestTypes(): RequestListCollection
    {
        return new RequestListCollection($this->service->getRequestTypes());
    }

    public function getRequestType(Document $requestType): RequestResource|JsonResponse
    {
        $response = $this->service->getRequestType($requestType->id);

        if (is_array($response) && array_key_exists('errors', $response)) {
            return $this->sendErrorResponse($response);
        }

        return new RequestResource($response);
    }

    public function getZbook()
    {
        $pdf = $this->service->getZbookPdf();
        $response = $pdf->download('zbook.pdf');
        $response->headers->set('Content-Type', 'application/pdf');
        return $response;
    }

    public function getTicket()
    {
        $pdf = $this->service->getTicketPdf();
        $response = $pdf->download('ticket.pdf');
        $response->headers->set('Content-Type', 'application/pdf');
        return $response;
    }

    /**
     * Генерирует QR код для документа
     *
     * @param QrCodeRequest $request
     * @return JsonResponse
     */
    public function generateQrCode(QrCodeRequest $request): JsonResponse
    {
        $size = $request->validated()['size'] ?? 200;

        $response = $this->service->getDocumentQrCode($size);

        if (is_array($response) && array_key_exists('errors', $response)) {
            return $this->sendErrorResponse($response);
        }

        return $this->sendResponse($response);
    }

    public function getDocumentsTemplates(): JsonResponse
    {
        $service = app(DocumentTemplateService::class);
        $templates = $service->getDocumentsTemplates();

        return $this->sendResponse($templates);
    }
}
