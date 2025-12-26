<?php

namespace App\Traits;

use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Трейт для ленивой загрузки сервисов
 * Позволяет загружать зависимости только при их использовании
 */
trait LazyServiceLoader
{
    /**
     * Кэш для загруженных сервисов
     */
    private array $serviceCache = [];

    /**
     * Ленивая загрузка сервиса по интерфейсу
     *
     * @param string $interface Интерфейс сервиса
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function getService(string $interface): mixed
    {
        if (!isset($this->serviceCache[$interface])) {
            $this->serviceCache[$interface] = app($interface);
        }

        return $this->serviceCache[$interface];
    }

    /**
     * Ленивая загрузка сервиса по классу
     *
     * @param string $className Класс сервиса
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function getServiceByClass(string $className): mixed
    {
        if (!isset($this->serviceCache[$className])) {
            $this->serviceCache[$className] = app($className);
        }

        return $this->serviceCache[$className];
    }

    /**
     * Очистка кэша сервисов
     */
    protected function clearServiceCache(): void
    {
        $this->serviceCache = [];
    }

    /**
     * Проверка, загружен ли сервис
     *
     * @param string $key Ключ сервиса
     * @return bool
     */
    protected function isServiceLoaded(string $key): bool
    {
        return isset($this->serviceCache[$key]);
    }
}
