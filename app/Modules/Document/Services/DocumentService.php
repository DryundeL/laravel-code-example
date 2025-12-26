<?php

namespace App\Modules\Document\Services;

use App\Modules\Document\Models\Document;
use App\Modules\Document\Traits\ProcessDocument;
use App\Modules\Document\Traits\DocumentHelper;
use App\Services\BaseService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Modules\Document\Interfaces\DataTransfer\DocumentServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class DocumentService extends BaseService implements DocumentServiceInterface
{
    use ProcessDocument, DocumentHelper;

    public function __construct(Document $document)
    {
        $this->model = $document;
    }

    public function getDocuments(): array
    {
        $identKeys = ['zbook'];
        $payloadData = $this->prepareDataToESB(true, true, $identKeys);
        $data = $this->handleRequestToESB('getDeanery', $payloadData);

        if (array_key_exists('errors', $data)) {
            return $data;
        }

        $requests = $data['requests'];
        $documents = $data['documents'];
        $signature = $data['signature'];

        array_walk($requests, function (&$request) {
            $this->processRequest($request);
        });

        if (!empty($documents)) {
            $this->processDocuments($documents);
        }

        if (isset($signature['dateBegin'], $signature['dateEnd'])) {
            $signature['dateBegin'] = Carbon::parse($signature['dateBegin'])->format('d.m.Y H:i:s');
            $signature['dateEnd'] = $signature['dateEnd'] !== '0001-01-01T00:00:00' ? Carbon::parse($signature['dateEnd'])->format('d.m.Y H:i:s') : null;
        }

        return [
            'signature' => $signature,
            'requests' => $requests,
            'documents' => $documents,
        ];
    }

    public function getVoContractData(): ?array
    {
        $identKeys = ['zbook'];
        $payloadData = $this->prepareDataToESB(true, true, $identKeys);
        $documents = $this->handleRequestToESB('getDeaneryDocuments', $payloadData, 'documents');

        foreach ($documents as $document) {
            if (isset($document['title']) && $document['title'] === 'Договор ВО') {
                return [
                    'uid' => $document['uid'],
                    'file_signed' => $document['fileSigned'],
                    'source' => $document['source'],
                ];
            }
        }

        return null;
    }

    public function getRequestTypes(): Collection
    {
        return $this->model->findInUses()->orderBy('id', 'asc')->get();
    }

    public function getRequestType(int $requestTypeId): Document|array
    {
        $requestType = $this->find($requestTypeId);

        if (!$requestType->inuse) {
            return $this->getErrorMessage('requestType', 'Ошибка доступа');
        }

        if ($requestType->type === 'expel') {
            $requestType->template_url = config('inStudy.yandex_s3_url') . 'documents/agreement.docx';
        }

        if ($requestType->type === 'discount' || $requestType->type === 'discount_social') {
            $requestType->activeDiscounts = $this->getActiveDiscounts();
        }

        return $requestType;
    }

    public function getZbookPdf()
    {
        $identKeys = ['zbook', 'semester'];
        $payloadData = $this->prepareDataToESB(true, true, $identKeys);
        $data = $this->handleRequestToESB('getZbook', $payloadData);

        $data = [
            ...$data,
            'userId' => Auth::user()->external_id,
            'retake' => $this->getZbookRetake($data['reportCardData']['interimAssessmentResults']['reExams'] ?? null),
            'certification' => $this->getZbookCertification($data['reportCardData']['interimAssessmentResults']['compulsoryDisciplines'] ?? null),
            'imgUrl' => env('YANDEX_S3_URL')
        ];

        $pdf = PDF::loadView('document::zbook.zbook', $data);
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf;
    }

    public function getTicketPdf()
    {
        $data = $this->getDocInfo();
        $data['qr'] = $this->getDocumentQrCode();

        $pdf = PDF::loadView('document::ticket', $data);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Serif');
        $pdf->setOption('encoding', 'UTF-8');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isPhpEnabled', true);
        $pdf->setOption('defaultMediaType', 'print');
        $pdf->setOption('isFontSubsettingEnabled', true);

        return $pdf;
    }

    public function getActiveDiscounts(): array
    {
        $identKeys = ['zbook'];
        $payloadData = $this->prepareDataToESB(true, true, $identKeys);
        $finances = Cache::remember('profile_' . Auth::user()->id . '_finances', 1800, function () use ($payloadData) {
            $constractDoc = ['contract' => $this->getVoContractData()];
            return array_merge($this->handleRequestToESB('getFinance', $payloadData), $constractDoc);
        });

        if (array_key_exists('errors', $finances)) {
            return $finances;
        }

        return $finances['finance']['activeDiscounts'] ?? [];
    }

}
