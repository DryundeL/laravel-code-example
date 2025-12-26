<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {name? : Название модуля}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создает новый модуль с полной структурой папок и файлов';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = $this->argument('name') ?? $this->ask('Введите название модуля');

        if (empty($moduleName)) {
            $this->error('Название модуля не может быть пустым!');
            return 1;
        }

        $moduleName = Str::studly($moduleName);
        $modulePath = app_path("Modules/{$moduleName}");

        // Проверяем, не существует ли уже модуль
        if (File::exists($modulePath)) {
            $this->error("Модуль '{$moduleName}' уже существует!");
            return 1;
        }

        $this->info("Создание модуля: {$moduleName}");

        // Создаем основную папку модуля
        File::makeDirectory($modulePath, 0755, true);

        // Спрашиваем про миграции
        $hasMigrations = $this->confirm('Будут ли миграции в этом модуле?', true);

        // Спрашиваем про конфиг
        $hasConfig = $this->confirm('Будет ли папка Config в этом модуле?', false);

        // Создаем структуру папок
        $this->createModuleStructure($modulePath, $moduleName, $hasMigrations, $hasConfig);

        // Создаем файлы
        $this->createModuleFiles($modulePath, $moduleName, $hasMigrations, $hasConfig);

        $this->info("Модуль '{$moduleName}' успешно создан!");
        $this->line("Путь: {$modulePath}");

        return 0;
    }

    /**
     * Создает структуру папок модуля
     */
    private function createModuleStructure(string $modulePath, string $moduleName, bool $hasMigrations, bool $hasConfig): void
    {
        $directories = [
            'Http/Controllers',
            'Http/Requests',
            'Models',
            'Providers',
            'Routes',
            'Services',
        ];

        if ($hasMigrations) {
            $directories[] = 'Database/Migrations';
        }

        if ($hasConfig) {
            $directories[] = 'Config';
        }

        foreach ($directories as $directory) {
            $fullPath = "{$modulePath}/{$directory}";
            File::makeDirectory($fullPath, 0755, true);
            $this->line("Создана папка: {$directory}");
        }
    }

    /**
     * Создает файлы модуля
     */
    private function createModuleFiles(string $modulePath, string $moduleName, bool $hasMigrations, bool $hasConfig): void
    {
        // Создаем ServiceProvider
        $this->createServiceProvider($modulePath, $moduleName, $hasMigrations, $hasConfig);

        // Создаем RouteServiceProvider
        $this->createRouteServiceProvider($modulePath, $moduleName);

        // Создаем файлы маршрутов
        $this->createRouteFiles($modulePath);
    }

    /**
     * Создает основной ServiceProvider
     */
    private function createServiceProvider(string $modulePath, string $moduleName, bool $hasMigrations, bool $hasConfig): void
    {
        $serviceProviderName = "{$moduleName}ServiceProvider";
        $serviceProviderPath = "{$modulePath}/Providers/{$serviceProviderName}.php";

        $migrationCode = $hasMigrations
            ? "        \$this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');\n"
            : '';

        $configCode = $hasConfig
            ? "        // \$this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'config');\n"
            : '';

        $content = "<?php

namespace App\\Modules\\{$moduleName}\\Providers;

use Illuminate\\Support\\ServiceProvider;

class {$serviceProviderName} extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \$this->app->register(RouteServiceProvider::class);
{$migrationCode}{$configCode}
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
    }
}";

        File::put($serviceProviderPath, $content);
        $this->line("Создан файл: Providers/{$serviceProviderName}.php");
    }

    /**
     * Создает RouteServiceProvider
     */
    private function createRouteServiceProvider(string $modulePath, string $moduleName): void
    {
        $routeServiceProviderPath = "{$modulePath}/Providers/RouteServiceProvider.php";

        $content = "<?php

namespace App\\Modules\\{$moduleName}\\Providers;

use Illuminate\\Foundation\\Support\\Providers\\RouteServiceProvider as ServiceProvider;
use Illuminate\\Support\\Facades\\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        \$this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(__DIR__ . '/../Routes/api.php');

            Route::middleware(['web', 'web.auth'])
                ->group(__DIR__ . '/../Routes/web.php');
        });
    }
}";

        File::put($routeServiceProviderPath, $content);
        $this->line("Создан файл: Providers/RouteServiceProvider.php");
    }

    /**
     * Создает файлы маршрутов
     */
    private function createRouteFiles(string $modulePath): void
    {
        // API routes
        $apiRoutesPath = "{$modulePath}/Routes/api.php";
        $apiContent = "<?php

use Illuminate\\Support\\Facades\\Route;

// API маршруты модуля
// Route::get('/example', [ExampleController::class, 'index']);";

        File::put($apiRoutesPath, $apiContent);
        $this->line("Создан файл: Routes/api.php");

        // Web routes
        $webRoutesPath = "{$modulePath}/Routes/web.php";
        $webContent = "<?php

use Illuminate\\Support\\Facades\\Route;

// Web маршруты модуля
// Route::get('/example', [ExampleController::class, 'index']);";

        File::put($webRoutesPath, $webContent);
        $this->line("Создан файл: Routes/web.php");
    }
}
