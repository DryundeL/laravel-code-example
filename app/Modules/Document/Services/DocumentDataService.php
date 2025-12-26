<?php

namespace App\Modules\Document\Services;

use App\Modules\Document\Traits\Imobis;
use App\Modules\Document\Traits\ProcessDocument;
use App\Modules\Document\Traits\DocumentHelper;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Container\BindingResolutionException;
use Exception;
use App\Modules\Document\Models\Document;

class DocumentDataService extends BaseService
{
    use ProcessDocument, DocumentHelper, Imobis;

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function __construct(Document $document)
    {
        $this->initializeImobis();
        $this->model = $document;
    }

    /**
     * Initialize Imobis trait properties
     *
     * @throws \Exception
     */
    private function initializeImobis(): void
    {
        $this->baseUrl = config('inStudy.imobis_url');
        $this->apiToken = config('inStudy.imobis_api_token');
    }

    public function getDocumentFileData(array $attributes): string|array|null
    {
        $payloadData = $this->prepareDataToESB(false, false, attributes: $attributes);
        $document = $this->handleRequestToESB('getDeaneryDocumentFile', $payloadData);

        if ($document['error']) {
            return $this->getErrorMessage('document', $document['message']);
        }

        return $document['base64Data'] ?? null;
    }

    public function getRequestFileData(array $attributes): string|array|null
    {
        $payloadData = $this->prepareDataToESB(false, false, attributes: $attributes);
        $request = $this->handleRequestToESB('getDeaneryRequestFile', $payloadData);

        if (array_key_exists('errors', $request)) {
            return $request;
        }

        return $request['data'] ?? null;
    }

    public function signDocument(array $attributes): array
    {
        if (isset($attributes['file'])) {
            $result = $this->handleSingleFileUpload($attributes, 'file');

            if (array_key_exists('errors', $result)) {
                return $result;
            }

            [$attributes, $originalName] = $result;
            $attributes['file'] = [
                [
                    'path' => $originalName,
                    'ext' => 'zip',
                ],
            ];
        }

        $identKeys = ['zbook'];
        $payloadData = $this->prepareDataToESB(true, true, $identKeys, $attributes);
        $document = $this->handleRequestToESB('signDeaneryDocumentFile', $payloadData);

        if (array_key_exists('errors', $document)) {
            return $document;
        }

        if (isset($document['document'])) {
            return $this->processDocument($document['document']);
        }

        return $document['success'] ?? false;
    }

    public function signRequest(array $attributes): array|bool
    {
        if (isset($attributes['file'])) {
            $result = $this->handleSingleFileUpload($attributes, 'file');

            if (array_key_exists('errors', $result)) {
                return $result;
            }

            [$attributes, $originalName] = $result;
            $attributes['file'] = [
                [
                    'path' => $originalName,
                    'ext' => 'zip',
                ],
            ];
        }

        $identKeys = ['zbook'];
        $payloadData = $this->prepareDataToESB(true, true, $identKeys, $attributes);
        $request = $this->handleRequestToESB('signDeaneryRequestFile', $payloadData);

        if (array_key_exists('errors', $request)) {
            return $request;
        }

        if (isset($request['request'])) {
            return $this->processRequest($request['request']);
        }

        return $request['success'] ?? false;
    }

    public function confirmRequest(string $type, array $attributes): array
    {
        return $this->sendRequestToESB($type, $attributes);
    }

    public function rejectRequest(array $attributes): array
    {
        $identKeys = ['zbook'];
        $payloadData = $this->prepareDataToESB(true, true, $identKeys, attributes: $attributes);
        $document = $this->handleRequestToESB('rejectDeaneryRequest', $payloadData);

        if (array_key_exists('errors', $document)) {
            return $document;
        }

        if (isset($request['request'])) {
            return $this->processRequest($request['request']);
        }

        return $document['success'] ?? false;
    }

    public function sendCode(): ?array
    {
        $contacts = Cache::get('profile_' . Auth::user()->id . '_contacts');

        if (empty($contacts)) {
            return $this->getErrorMessage('sms', 'Номер телефона не найден');
        }

        $phone = $contacts['phoneM'];
        $email = strtolower($contacts['email']);
        return $this->sendSMS($phone, $email);
    }

    public function getNewEdsFileData(): array
    {
        $identKeys = ['zbook'];
        $payloadData = $this->prepareDataToESB(true, true, $identKeys);
        $response = $this->handleRequestToESB('getDeaneryNewSignData', $payloadData);

        if (array_key_exists('errors', $response)) {
            return $response;
        }

        return $this->processSignNew($response);
    }

    public function sendNewEds(int $code): array
    {
        $isVerified = $this->confirmCode($code);

        if (!$isVerified) {
            return $this->getErrorMessage('code', 'Неверный код');
        }

        $identKeys = ['zbook'];
        $payloadData = $this->prepareDataToESB(true, true, $identKeys);
        $response = $this->handleRequestToESB('setDeaneryNewSign', $payloadData);

        if (array_key_exists('errors', $response)) {
            return $response;
        }

        $request = $response['data'];
        $eds = $response['response1C']['signatureData'] ?? null;
        if ($eds) {
            if (isset($eds['dateBegin'], $eds['dateEnd'])) {
                $eds['dateBegin'] = Carbon::parse($eds['dateBegin'])->format('d.m.Y H:i:s');
                $eds['dateEnd'] = $eds['dateEnd'] !== '0001-01-01T00:00:00' ? Carbon::parse($eds['dateEnd'])->format('d.m.Y H:i:s') : null;
            }
        }

        $response['request'] = $this->processRequest($request);
        $response['eds'] = $eds;

        return $response;
    }

    public function getRequestOptions(string $requestType): array
    {
        $identKeys = ['zbook'];
        $attributes['type'] = $requestType;
        $payloadData = $this->prepareDataToESB(true, true, $identKeys, $attributes);
        $response = $this->handleRequestToESB('getDeaneryData', $payloadData);

        if (array_key_exists('errors', $response)) {
            return $response;
        }

        return match ($requestType) {
            'transfer', 'discount_social', 'personal', 'expel', 'academ' => $response,
            'discount' => $this->processDiscount($response),
            default => [],
        };
    }

    public function sendRequest(string $requestType, array $attributes): array|bool
    {
        switch ($requestType) {
            case 'discount_social':
                $result = $this->handleSingleFileUpload($attributes, 'file');

                if (array_key_exists('errors', $result)) {
                        return $result;
                }

                [$attributes, $originalName] = $result;
                $attributes['files'] = [
                    [
                        'path' => $originalName,
                        'ext' => 'zip',
                    ],
                ];

                unset($attributes['file']);
                break;

            case 'discount':
                $sum = $this->calculateSum($attributes);

                if (is_array($sum) && array_key_exists('errors', $sum)) {
                    return $sum;
                }

                $attributes['sum'] = $sum;
                break;
            case 'academ':
            case 'expel':
                if (isset($attributes['file'])) {
                    $result = $this->handleSingleFileUpload($attributes, 'file');

                    if (array_key_exists('errors', $result)) {
                        return $result;
                    }

                    [$attributes, $originalName] = $result;
                    $attributes['files'] = [
                        [
                            'path' => $originalName,
                            'ext' => 'zip',
                        ],
                    ];

                    unset($attributes['file']);
                    break;
                }

                break;
            case 'personal':
                if (isset($attributes['doc_date'])) {
                    $attributes['doc_date'] = Carbon::parse($attributes['doc_date'])->format('Y-m-d');
                }

                $attributes['files'] = [];

                if (isset($attributes['personal_file'])) {
                    $result = $this->handleSingleFileUpload($attributes, 'personal_file');

                    if (array_key_exists('errors', $result)) {
                        return $result;
                    }

                    [$attributes, $originalName] = $result;
                    $attributes['files'][] = [
                        'path' => $originalName,
                        'ext' => 'zip',
                        'type' => '000000090',
                    ];

                    unset($attributes['personal_file']);
                }

                if (isset($attributes['change_file'])) {
                    $result = $this->handleSingleFileUpload($attributes, 'change_file');

                    if (array_key_exists('errors', $result)) {
                        return $result;
                    }

                    [$attributes, $originalName] = $result;
                    $attributes['files'][] = [
                        'path' => $originalName,
                        'ext' => 'zip',
                        'type' => '000000145',
                    ];

                    unset($attributes['change_file']);
                }
                break;

            case 'iup':
                $attributes['semestr'] = (int)$attributes['semester'];
                $attributes['accelerated'] = isset($attributes['accelerated']) && $attributes['accelerated'];

                $result = $this->handleSingleFileUpload($attributes, 'file');

                if (array_key_exists('errors', $result)) {
                    return $result;
                }

                [$attributes, $originalName] = $result;
                $attributes['files'] = [
                    [
                        'path' => $originalName,
                        'ext' => 'zip',
                    ],
                ];

                unset($attributes['file'], $attributes['semester']);
                break;

            default:
                break;
        }

        return $this->sendRequestToESB($requestType, $attributes);
    }

    private function sendRequestToESB(string $type, array $data): array
    {
        $existingRequests = $this->checkExistingRequests($type);

        if ($existingRequests) {
            return $this->getErrorMessage('document', 'У вас уже есть активный запрос этого типа. Дождитесь его обработки или отмените существующий запрос.');
        }

        $identKeys = ['gid', 'zbook'];
        $attributes = [
            'type' => $type,
            'data' => $data,
        ];
        $payloadData = $this->prepareDataToESB(true, true, $identKeys, $attributes);
        $document = $this->handleRequestToESB('sendToDeanery', $payloadData);

        if (array_key_exists('errors', $document)) {
            return $document;
        }

        if ($document['success']) {
            return $this->processRequest($document['data']);
        }

        return $this->getErrorMessage('document', 'Ошибка при отправке запроса');
    }
}
