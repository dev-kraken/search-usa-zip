<?php

declare(strict_types=1);

namespace DevKraken\Cache;

interface CacheInterface
{
    public function get(string $key): mixed; // Specify return type as mixed for flexibility

    public function set(string $key, mixed $value, int $ttl = 3600): bool; // Specify return type as bool

    public function delete(string $key): bool;
}