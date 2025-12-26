<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profiles', static function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->unsignedInteger('external_id')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('group')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('access_id')->constrained();


            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('middle_name');
            $table->dropColumn('photo_url');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('photo_url')->nullable();
        });

        Schema::dropIfExists('profiles');
    }
};
