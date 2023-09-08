<?php
/**
 * Created by PhpStorm.
 * User: Jaeger <JaegerCode@gmail.com>
 * Date: 18/12/11
 * Time: 下午6:39
 */

namespace Jaeger;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\CacheInterface;

class Cache extends GHttp
{
    public static function remember($name, $arguments)
    {
        $cachePool = null;
        $cacheConfig = self::initCacheConfig($arguments);

        if (empty($cacheConfig['cache'])) {
            return self::$name(...$arguments);
        }
        if (is_string($cacheConfig['cache'])) {
            $cachePool = new FilesystemAdapter("", 0, $cacheConfig['cache']);
        }else if ($cacheConfig['cache'] instanceof CacheInterface) {
            $cachePool = $cacheConfig['cache'];
        }

        $cacheKey = self::getCacheKey($name,$arguments);
        $cache = $cachePool->getItem($cacheKey);
        print_r($cache->get());
        print_r($cacheKey);
        if(!$cache->isHit()) {
            $data = self::$name(...$arguments);
            if(!empty($data)) {
                $cache->set($data);
                if (!empty($cacheConfig['cache_ttl'])) {
                    $cache->expiresAfter($cacheConfig['cache_ttl']);
                }
                $cachePool->save($cache);
            }
            return $data;
        }
        return $cache->get();
    }

    protected static function initCacheConfig($arguments)
    {
        $cacheConfig = [
            'cache' => null,
            'cache_ttl' => null
        ];
        if(!empty($arguments[2])) {
            $cacheConfig = array_merge([
                'cache' => null,
                'cache_ttl' => null
            ],$arguments[2]);
        }
        return $cacheConfig;
    }

    protected static function getCacheKey($name, $arguments)
    {
        return md5($name.'_'.json_encode($arguments));
    }
}