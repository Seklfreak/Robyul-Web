<?php

namespace RobyulWebBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Unirest;

class RobyulApi
{
    private $container;
    private $redis;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->redis = $this->container->get('snc_redis.default');
    }

    public function getRequest($endpoint, $expire = '+1 hour')
    {
        $key = 'robyul2-web:api:' . md5($endpoint);
        if ($this->redis->exists($key) == true) {
            $data = unserialize($this->redis->get($key));
        } else {
            $data = Unirest\Request::get('http://localhost:2021/' . $endpoint,
            array(
                'Authorization' => 'Webkey '.$this->container->getParameter('bot_webkey'),
                'User-Agent' => 'Robuyl-Web/0.1' // TODO: version
            ));
            $data = (array)$data->body;

            $this->redis->set($key, serialize($data));
            $this->redis->expireat($key, strtotime($expire));
        }
        return $data;
    }
    
    public function getRequestRaw($endpoint, $expire = '+1 hour')
    {
        $key = 'robyul2-web:api:raw:' . md5($endpoint);
        if ($this->redis->exists($key) == true) {
            $data = unserialize($this->redis->get($key));
        } else {
            $data = Unirest\Request::get('http://localhost:2021/' . $endpoint,
            array(
            'Authorization' => 'Webkey '.$this->container->getParameter('bot_webkey'),
            'User-Agent' => 'Robuyl-Web/0.1' // TODO: version
            ));
            $data = (string)$data->body;
    
            $this->redis->set($key, serialize($data));
            $this->redis->expireat($key, strtotime($expire));
        }
        return $data;
    }
}
