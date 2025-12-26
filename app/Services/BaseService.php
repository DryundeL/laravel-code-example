<?php

namespace App\Services;

use App\Modules\User\Models\Profile;
use App\Traits\iESBRequest;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BaseService
{
    use iESBRequest;

    /**
     * References model.
     */
    protected Model $model;

    protected OrganizationHandlerResolver $resolver;

    private ?LoggerService $logger = null;
    private ?string $esbUrl = null;
    private ?string $esbAdapter = null;


    /**
     * Store a newly created resource in storage.
     *
     * @param Model $model
     */
    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    /**
     * @throws BindingResolutionException
     */
    protected function createOrganizationHandler(
        string $moduleName,
        OrganizationHandlerResolver $resolver,
        string $handlerGroup = null
    ): object
    {
        return $resolver->getHandler($moduleName, $handlerGroup);
    }

    public function create(array $attributes)
    {
        return DB::transaction(function () use ($attributes) {
            $model = $this->model;

            $model->fill($attributes);
            $model->save();
            $model->refresh();

            return $model;
        });
    }

    public function update(array $attributes, int $id)
    {
        return DB::transaction(function () use ($attributes, $id) {
            $model = $this->find($id);
            $model->update($attributes);
            $model->refresh();

            return $model;
        });
    }

    public function find(int $id): mixed
    {
        return $this->model->find($id);
    }

    public function destroy(int $id): mixed
    {
        return $this->find($id)->delete();
    }

    protected function handleRequestToESB(string $bus, array $payloadData, string $key = null): mixed
    {
        $data = $this->sendESBRequest($bus, $payloadData);

        if ($data === null) {
            return $this->getErrorMessage('error', 'Внутренняя ошибка');
        }

        if ($data['error']) {
            return $this->getErrorMessage('error', $data['text']);
        }

        if ($key) {
            return $data['payload'][$key] ?? null;
        }

        return $data['payload'];
    }

    protected function prepareDataToESB(
        bool    $needProfile,
        bool  $includeIdent = false,
        array $identKeys = null,
        array   $attributes = null,
        Profile $profile = null,
    ): array
    {
        if ($needProfile) {
            $externalProfileId = Auth::user()->external_id;
        } elseif ($profile) {
            $externalProfileId = $profile->external_id;
        } else {
            $externalProfileId = null;
        }

        $payload = [];

        if ($externalProfileId) {
            $payload['user_id'] = $externalProfileId;
        }

        if ($attributes) {
            $payload = array_merge($payload, $attributes);
        }

        return [
            'payload' => $payload,
            'ESBId' => $externalProfileId,
            'includeIdent' => $includeIdent,
            'identKeys' => $identKeys,
            'profile' => $profile,
        ];
    }

    public function getErrorMessage(string $key, string $message): array
    {
        return [
            'errors' => [
                $key => [
                    $message
                ]
            ]
        ];
    }

    public function deleteFile($url, string $folderName, bool $hasThumb = false): bool
    {
        if ($url !== null) {
            $deleteFileName = explode('/', $url);
            $deleteFileName = $deleteFileName[count($deleteFileName) - 1];
            Storage::disk('yandexCloud')->delete($folderName . '/' . $deleteFileName);

            return true;
        }

        return false;
    }

    public function addFile(UploadedFile $file, string $folderName, string $customFileName = ''): string
    {
        $yandexS3Url = config('inStudy.yandex_s3_url');

        if ($customFileName !== '') {
            $fileName = $customFileName . '.' . $file->getClientOriginalExtension();
        } else {
            $fileName = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        }

        $filePath = Storage::disk('yandexCloud')->putFileAs($folderName, $file, $fileName);

        return $yandexS3Url . $filePath;
    }

    protected function getLogger(): LoggerService
    {
        return $this->logger ??= new LoggerService();
    }

    protected function getEsbUrl(): string
    {
        return $this->esbUrl ??= config('inStudy.ESB_url') ?? throw new \RuntimeException('ESB URL not configured');
    }

    protected function getEsbAdapter(): string
    {
        return $this->esbAdapter ??= config('inStudy.ESB_adapter', '65f0118f6bbe2');
    }
}
