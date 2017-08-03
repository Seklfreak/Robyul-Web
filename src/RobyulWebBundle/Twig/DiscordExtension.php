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
            new \Twig_Function('discord_avatar', array($this, 'getDiscordAvatar')),
            new \Twig_Function('user_rank_data', array($this, 'getUserRankData')),
        );
    }

    public function getDiscordAvatar($userID, $hash)
    {
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
            $rankingInfo = Unirest\Request::get('http://localhost:2021/rankings/user/' . $userID . '/' . $guildID);
            $rankingData = (array)$rankingInfo->body;

            $redis->set($key, serialize($rankingData));
            $redis->expireat($key, strtotime("+30 minutes"));
        }

        return $rankingData;
    }
}