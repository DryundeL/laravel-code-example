<?php

use App\Modules\User\Models\Profile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('calendar_token')->nullable();
        });

        foreach (Profile::all() as $profile) {
            $profile->calendar_token = Str::random(32);
            $profile->save();
        }
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('calendar_token');
        });
    }
};
