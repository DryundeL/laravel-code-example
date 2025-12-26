<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('ALTER TABLE modules ALTER COLUMN "show" DROP DEFAULT');
        DB::statement('ALTER TABLE modules ALTER COLUMN "show" TYPE jsonb USING (CASE WHEN "show" = true THEN \'["mobile", "desktop"]\'::jsonb ELSE \'[]\'::jsonb END)');
        DB::statement('ALTER TABLE modules ALTER COLUMN "show" DROP NOT NULL');
        DB::statement('ALTER TABLE modules ALTER COLUMN "show" SET DEFAULT \'["mobile", "desktop"]\'::jsonb');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE modules ALTER COLUMN "show" DROP DEFAULT');
        DB::statement('ALTER TABLE modules ALTER COLUMN "show" TYPE boolean USING ("show"->>\'mobile\' IS NOT NULL OR "show"->>\'desktop\' IS NOT NULL)');
        DB::statement('ALTER TABLE modules ALTER COLUMN "show" SET NOT NULL');
        DB::statement('ALTER TABLE modules ALTER COLUMN "show" SET DEFAULT false');
    }
};
