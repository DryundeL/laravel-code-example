<?php

namespace App\Modules\Document\Interfaces\DataTransfer;


interface DocumentServiceInterface
{
    public function getDocuments(): array;

    public function getVoContractData(): ?array;
}
