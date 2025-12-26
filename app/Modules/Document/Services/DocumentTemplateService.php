<?php

namespace App\Modules\Document\Services;

use App\Services\BaseService;
use Illuminate\Support\Facades\Storage;

class DocumentTemplateService extends BaseService
{
    public function getDocumentsTemplates(): array
    {
        $applicationForms = Storage::disk('yandexCloud')->files('documents/application-forms');
        $yandexS3Url = config('inStudy.yandex_s3_url');

        $applicationForms = array_map(fn (string $applicationForm) => [
            'name' => pathinfo($applicationForm, PATHINFO_FILENAME),
            'url' => $yandexS3Url . $applicationForm,
        ], $applicationForms);

        $orders = Storage::disk('yandexCloud')->files('documents/orders-on-training-costs');
        $orders = array_map(fn (string $order) => [
            'name' => pathinfo($order, PATHINFO_FILENAME),
            'url' => $yandexS3Url . $order,
        ], $orders);

        return [
            'applicationForms' => $applicationForms,
            'orders' => $orders,
        ];
    }
}
