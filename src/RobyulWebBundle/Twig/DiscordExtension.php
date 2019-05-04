<?php

namespace RobyulWebBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Unirest;

class DiscordExtension extends \Twig_Extension
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_Function('discord_user', array($this, 'getDiscordUser')),
            new \Twig_Function('discord_avatar', array($this, 'getDiscordAvatar')),
            new \Twig_Function('user_rank_data', array($this, 'getUserRankData')),
        );
    }

    public function getDiscordUser($userID)
    {
        $redis = $this->container->get('snc_redis.default');

        $key = 'robyul2-web:api:user:' . $userID;
        if ($redis->exists($key) == true) {
            $userData = unserialize($redis->get($key));
        } else {
            $userInfo = Unirest\Request::get($this->container->getParameter('bot_api_base_url') . 'user/' . $userID, array('Authorization' => 'Webkey ' . $this->container->getParameter('bot_webkey')));
            $userData = (array)$userInfo->body;

            $redis->set($key, serialize($userData));
            $redis->expireat($key, strtotime("+15 minutes"));
        }

        return $userData;
    }

    public function getDiscordAvatar($userID, $hash)
    {
        if ($hash == '') {
            return $this->container->get('assets.packages')->getUrl('static/images/placeholder_icon.jpg');
        }

        if (substr($hash, 0, 2) === "a_") {
            return 'https://cdn.discordapp.com/avatars/' . $userID . '/' . $hash . '.gif';
        } else {
            return 'https://cdn.discordapp.com/avatars/' . $userID . '/' . $hash . '.png';
        }
    }

    public function getUserRankData($guildID, $userID)
    {
        $redis = $this->container->get('snc_redis.default');

        $key = 'robyul2-web:api:levels:ranking:' . $guildID . ':by-user:' . $userID;
        if ($redis->exists($key) == true) {
            $rankingData = unserialize($redis->get($key));
        } else {
            $rankingInfo = Unirest\Request::get($this->container->getParameter('bot_api_base_url') . 'rankings/user/' . $userID . '/' . $guildID, array('Authorization' => 'Webkey ' . $this->container->getParameter('bot_webkey')));
            $rankingData = (array)$rankingInfo->body;

            $redis->set($key, serialize($rankingData));
            $redis->expireat($key, strtotime("+30 minutes"));
        }

        return $rankingData;
    }
}