<?php

declare(strict_types=1);

namespace DevKraken\Cache;

use Predis\Client;
use Predis\Response\Status;

class RedisClient implements CacheInterface
{
    private Client $redis;

    public function __construct(string $host = REDIS_HOST, int $port = REDIS_PORT)
    {
        $this->redis = new Client([
            'scheme' => 'tcp',
            'host' => $host,
            'port' => $port,
        ]);
    }

    public function get(string $key): mixed
    {
        $value = $this->redis->get($key);
        if ($value === null) {
            return null;
        }

        // Specify allowed classes for serialization
        return unserialize($value, [
            'allowed_classes' => [
                // List allowed classes here, for example:
                // 'MyApp\DTO\UserDTO',
                // 'MyApp\DTO\ProductDTO',
            ]
        ]);
    }

    public function set(string $key, mixed $value, int $ttl = REDIS_CACHE_EXP): bool
    {
        $status = $this->redis->setex($key, $ttl, serialize($value));
        return $status instanceof Status && $status->getPayload() === 'OK';
    }

    public function delete(string $key): bool
    {
        return $this->redis->del([$key]) > 0;
    }
}