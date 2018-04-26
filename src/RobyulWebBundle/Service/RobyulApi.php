<?php

namespace RobyulWebBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Unirest;
use Psr\Log\LoggerInterface;

class RobyulApi
{
    private $container;
    private $redis;
    private $logger;

    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->redis = $this->container->get('snc_redis.default');
        $this->logger = $logger;
    }

    protected function logRequest($method, $url, $as, $took)
    {
        $this->logger->info($method . ' ' . $url . ' as ' . $as . ' took ' . $took . 's');
    }

    public function invalidate($endpoint)
    {
        $key = 'robyul2-web:api:' . md5($endpoint);
        if ($this->redis->exists($key) == true) {
            $this->redis->del($key);
        }
    }

    public function getRequest($endpoint, $expire = '+1 hour', $fresh = false)
    {
        $key = 'robyul2-web:api:' . md5($endpoint);
        if ($fresh == false && $this->redis->exists($key) == true) {
            $this->logger->info('HIT http://localhost:2021/' . $endpoint);
            $data = unserialize($this->redis->get($key));
        } else {
            $timeStart = microtime(true);
            $result = Unirest\Request::get('http://localhost:2021/' . $endpoint,
                array(
                    'Authorization' => 'Webkey ' . $this->container->getParameter('bot_webkey'),
                    'User-Agent' => 'Robuyl-Web/0.1' // TODO: version
                ));
            $timeEnd = microtime(true);
            $this->logRequest('GET', 'http://localhost:2021/' . $endpoint, 'json array', $timeEnd - $timeStart);
            $data = (array)$result->body;

            if ($expire != '' && ($result->code >= 200 && $result->code < 300)) {
                $this->redis->set($key, serialize($data));
                $this->redis->expireat($key, strtotime($expire));
            }
        }
        return $data;
    }

    public function getRequestRaw($endpoint, $expire = '+1 hour')
    {
        $key = 'robyul2-web:api:raw:' . md5($endpoint);
        if ($this->redis->exists($key) == true) {
            $data = unserialize($this->redis->get($key));
        } else {
            $timeStart = microtime(true);
            $data = Unirest\Request::get('http://localhost:2021/' . $endpoint,
                array(
                    'Authorization' => 'Webkey ' . $this->container->getParameter('bot_webkey'),
                    'User-Agent' => 'Robuyl-Web/0.1' // TODO: version
                ));
            $timeEnd = microtime(true);
            $this->logRequest('GET', 'http://localhost:2021/' . $endpoint, 'raw', $timeEnd - $timeStart);
            $data = (string)$data->body;

            $this->redis->set($key, serialize($data));
            $this->redis->expireat($key, strtotime($expire));
        }
        return $data;
    }
}
