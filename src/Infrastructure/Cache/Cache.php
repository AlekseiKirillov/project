<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use Psr\Cache\CacheItemPoolInterface;

final readonly class Cache
{
    public function __construct(
        private CacheItemPoolInterface $cache,
    ) {
    }

    /**
     * @template TValue
     * @param callable(): TValue $loader
     * @param ?positive-int $ttlSeconds
     * @return TValue
     */
    public function get(string $key, callable $loader, ?int $ttlSeconds = null): mixed
    {
        $item = $this->cache->getItem($key);

        if ($item->isHit()) {
            /** @var TValue */
            return $item->get();
        }

        $value = $loader();
        $item->set($value);
        $item->expiresAfter($ttlSeconds);
        $this->cache->save($item);
    }
}
