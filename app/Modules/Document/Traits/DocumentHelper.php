<?php

namespace App\Modules\Document\Traits;

use App\Modules\Document\Models\Document;
use App\Modules\Finance\Interfaces\DataTransfer\FinanceServiceInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;
use App\Modules\Document\Interfaces\DataTransfer\DocumentServiceInterface;
use App\Traits\LazyServiceLoader;

trait DocumentHelper
{
    use LazyServiceLoader;
    /**
     * Обработка загрузки одного файла
     *
     * @param array $attributes
     * @param string $fileKey
     * @return array
     */
    private function handleSingleFileUpload(array $attributes, string $fileKey): array
    {
        if (!isset($attributes[$fileKey]) || !($attributes[$fileKey] instanceof UploadedFile)) {
            return $this->getErrorMessage('upload', 'Файл не предоставлен');
        }

        $file = $attributes[$fileKey];
        $uploadResult = $this->uploadDoc($file, $file->getClientOriginalName());

        if (is_array($uploadResult) || !$uploadResult) {
            return $uploadResult;
        }

        return [$attributes, $uploadResult];
    }

    /**
     * Загрузка документа в ZIP архив
     *
     * @param UploadedFile $file
     * @param string $originalName
     * @return string|array
     */
    private function uploadDoc(UploadedFile $file, string $originalName): string|array
    {
        if (!file_exists($file)) {
            return $this->getErrorMessage('documents', 'Файл не найден.');
        }

        $zipName = md5(uniqid('', true));
        $zipExt = '.zip';

        if (!Storage::disk('documents')->exists('/')) {
            try {
                Storage::disk('documents')->makeDirectory('/');
            } catch (Exception $e) {
                return $this->getErrorMessage('documents', 'Не удалось создать директорию для архива.');
            }
        }

        $zipPath = Storage::disk('documents')->path($zipName . $zipExt);
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($file, $originalName);
            $zip->close();
        } else {
            return $this->getErrorMessage('documents', 'Не удалось создать ZIP-архив.');
        }

        return $zipName . $zipExt;
    }

    /**
     * Расчет суммы для скидок
     *
     * @param array $attributes
     * @return int|array
     */
    private function calculateSum(array $attributes): int|array
    {
        $profile = Auth::user();
        $semesterBegin = $attributes['semester_begin'] ?? null;
        $semesterEnd = $attributes['semester_end'] ?? null;
        $semesters = Cache::get('discount_' . $profile->external_id);

        if (!$semesters || !isset($semesters['semesters'])) {
            return $this->getErrorMessage('discount', 'Нет данных');
        }

        $semesterData = $semesters['semesters'];

        if ($semesterBegin === $semesterEnd) {
            $targetSemester = $semesterBegin ?? $semesterData[0]['semester'];
            foreach ($semesterData as $semester) {
                if ($semester['semester'] === $targetSemester) {
                    return (int)$semester['price'];
                }
            }
            return 0;
        }

        $sum = 0;
        foreach ($semesterData as $semester) {
            if ($semester['semester'] === $semesterBegin) {
                $sum += $semester['price'];
            }
            if ($semester['semester'] === $semesterEnd) {
                $sum += $semester['price'];
            }
        }

        return (int)$sum;
    }

    /**
     * Проверяет существующие активные запросы с указанным типом
     *
     * @param string $type Тип запроса
     * @return array|bool Массив активных запросов или false если нет активных запросов
     */
    private function checkExistingRequests(string $type): array|bool
    {
        $documentsService = $this->getService(DocumentServiceInterface::class);
        $data = $documentsService->getDocuments();

        if (array_key_exists('errors', $data)) {
            return $data;
        }

        if (empty($data['requests'])) {
            return false;
        }

        $hasActiveRequests = false;
        $activeStatusTypes = ['default', 'warning'];

        foreach ($data['requests'] as $request) {
            $requestType = $this->getDocumentTypeByTitle($request['title'] ?? '');

            if ($requestType === $type) {
                if (
                    isset($request['status']['type']) &&
                    in_array($request['status']['type'], $activeStatusTypes)
                ) {
                    $hasActiveRequests = true;
                    break;
                }
            }
        }

        return $hasActiveRequests;
    }

    /**
     * Определяет тип документа по его названию
     *
     * @param string $title Название документа
     * @return string|null Тип документа или null если не найден
     */
    private function getDocumentTypeByTitle(string $title): ?string
    {
        $document = Document::where('name', $title)->first();
        return $document ? $document->type : null;
    }



    /**
     * Получение информации о документе
     *
     * @return array
     */
    public function getDocInfo(): array
    {
        $profile = Auth::user();
        $profileData = json_decode(Cache::get('external_profile_' . $profile->external_id), true, 512, JSON_THROW_ON_ERROR);
        $user = $profile->user;

        $fio = trim($user->last_name . ' ' . $user->first_name . ' ' . $user->middle_name);
        $group = $profile->group;
        $financeService = app(FinanceServiceInterface::class);
        $contract = $financeService->getFinances()['contract']['contract_number'];
        $course = round($profileData['semester'] / 2);
        $eduForm = $profileData['eduFormName'];

        $purpose = $contract . ', ' . $fio . ', ' . $group . ', ' . $course . ' курс, ' . $eduForm;

        return [
            'purpose' => $purpose,
            'fio' => $fio
        ];
    }

    /**
     * Получает QR код для документа
     *
     * @param int $size Размер QR кода (по умолчанию 200)
     * @return string QR код в base64 формате
     */
    public function getDocumentQrCode(int $size = 200): string
    {
        $docInfo = $this->getDocInfo();
        $url = 'ST00012|Name=НОЧУ ВО «Московский институт психоанализа»|PersonalAcc=40703810238100100649|BankName=ПАО "СБЕРБАНК"|BIC=044525225|CorrespAcc=30101810400000000225|PayeeINN=7713131464|PayeeKPP=773001001|Purpose=/' . $docInfo['purpose'] . '|ChildFIO=' . $docInfo['fio'];

        $qrCode = QrCode::format('png')
            ->size($size)
            ->encoding('UTF-8')
            ->generate($url);

        return 'data:image/png;base64,' . base64_encode($qrCode);
    }
}
