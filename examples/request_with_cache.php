<?php
/**
 * Created by PhpStorm.
 * User: Jaeger <JaegerCode@gmail.com>
 * Date: 18/12/11
 * Time: 下午6:48
 */

require __DIR__.'/../vendor/autoload.php';
use Jaeger\GHttp;
use Cache\Adapter\Predis\PredisCachePool;
use Symfony\Component\Cache\Adapter\RedisAdapter;


$rt = GHttp::get('http://httpbin.org/get',[
    'wd' => 'QueryList'
],[
    'cache' => __DIR__,
    'cache_ttl' => 120
]);

print_r($rt);

$con = RedisAdapter::createConnection('redis://127.0.0.1:6379');
$pool = new RedisAdapter($con);

$rt = GHttp::get('http://httpbin.org/get',[
    'wd' => 'QueryList'
],[
    'cache' => $pool,
    'cache_ttl' => 120
]);

print_r($rt);