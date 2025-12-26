<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class DeleteCacheKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * {key} — Ключ, который будет удалён из кэша.
     *
     * @var string
     */
    protected $signature = 'delete:cache {key : Ключ, который нужно удалить}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаляет заданный ключ из кэша';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $key = $this->argument('key');

        if (Cache::forget($key)) {
            $this->info("Ключ '{$key}' успешно удалён.");
        } else {
            $this->warn("Не удалось удалить ключ '{$key}' или он не существует.");
        }

        return 0;
    }
}
