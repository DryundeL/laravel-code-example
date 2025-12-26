<?php

use App\Modules\Document\Models\Document;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documents', static function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });

        $document = [
            'name' => 'Заявление о предоставлении электронной подписи',
            'type' => 'sign_new',
            'inuse' => false,
        ];

        Document::create($document);
    }

    public function down(): void
    {
        Schema::table('documents', static function (Blueprint $table) {
            $table->text('description')->change();
        });
    }
};
