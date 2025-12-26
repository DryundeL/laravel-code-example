<?php

use Illuminate\Http\Request;

if (!function_exists('getClientIpAddress')) {

    /**
     * Проверяет, является ли IP адрес приватным (внутренним)
     *
     * @param string $ip
     * @return bool
     */
    function isPrivateIp(string $ip): bool
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        // Проверяем IPv4 приватные диапазоны
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        }

        // Проверяем IPv6 приватные диапазоны
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            // fc00::/7 - уникальные локальные адреса
            // fe80::/10 - link-local адреса
            return str_starts_with($ip, 'fc') || str_starts_with($ip, 'fe80');
        }

        return false;
    }

    /**
     * Получает реальный IP адрес клиента из заголовков
     *
     * Приоритет:
     * 1. X-Forwarded-For (первый публичный IP в цепочке - реальный IP клиента)
     * 2. X-Real-IP (если это публичный IP)
     * 3. REMOTE_ADDR (если это публичный IP)
     * 4. Fallback на стандартные методы Laravel
     *
     * @param Request $request
     * @return string|null
     */
    function getClientIpAddress(Request $request): ?string
    {
        // Сначала проверяем X-Forwarded-For - содержит цепочку IP: "клиент, прокси1, прокси2"
        $xForwardedFor = $request->header('X-Forwarded-For');
        if ($xForwardedFor) {
            $ips = array_map('trim', explode(',', $xForwardedFor));
            foreach ($ips as $ip) {
                $ip = trim($ip, '()');
                if (filter_var($ip, FILTER_VALIDATE_IP) && !isPrivateIp($ip)) {
                    return $ip; // Возвращаем первый публичный IP (реальный IP клиента)
                }
            }
            // Если все IP приватные, возвращаем первый (на случай если клиент действительно в приватной сети)
            $firstIp = trim($ips[0] ?? '', '()');
            if (filter_var($firstIp, FILTER_VALIDATE_IP)) {
                return $firstIp;
            }
        }

        // Fallback на стандартные методы Laravel
        $ip = $request->ip();
        if ($ip) {
            $ip = trim(explode(',', $ip)[0]);
            $ip = trim($ip, '()');
            if (filter_var($ip, FILTER_VALIDATE_IP) && !isPrivateIp($ip)) {
                return $ip;
            }
        }

        return null;
    }
}

