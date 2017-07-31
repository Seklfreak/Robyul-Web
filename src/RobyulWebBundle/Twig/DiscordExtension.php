<?php

namespace RobyulWebBundle\Twig;

class DiscordExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_Function('discord_avatar', array($this, 'getDiscordAvatar')),
        );
    }

    public function getDiscordAvatar($userID, $hash)
    {
        if (substr($hash, 0, 2) === "a_") {
            return 'https://cdn.discordapp.com/avatars/'.$userID.'/'.$hash.'.gif';
        } else {
            return 'https://cdn.discordapp.com/avatars/'.$userID.'/'.$hash.'.png';
        }
    }
}