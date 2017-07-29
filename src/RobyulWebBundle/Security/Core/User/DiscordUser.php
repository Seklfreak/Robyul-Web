<?php

namespace RobyulWebBundle\Security\Core\User;


use Symfony\Component\Security\Core\User\UserInterface;

class DiscordUser implements UserInterface, \Serializable
{
    protected $id;
    protected $username;
    protected $discriminator;
    protected $avatarHash;

    public function __construct($id, $username, $discriminator, $avatarHash)
    {
        $this->id = $id;
        $this->username = $username;
        $this->discriminator = $discriminator;
        $this->avatarHash = $avatarHash;
    }

    public function getRoles()
    {
        return array('ROLE_USER', 'ROLE_OAUTH_USER', 'ROLE_DISCORD_USER');
    }

    public function getPassword()
    {
        return null;
    }

    public function getSalt()
    {
        return null;
    }

    public function GetID()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->id;
    }

    public function GetDiscordUsername()
    {
        return $this->username;
    }

    public function getDiscriminator()
    {
        return $this->discriminator;
    }

    public function getAvatarHash()
    {
        return $this->avatarHash;
    }

    public function getAvatarUrl()
    {
        if (substr($this->avatarHash, 0, 2) === "a_") {
            return 'https://cdn.discordapp.com/avatars/'.$this->id.'/'.$this->avatarHash.'.gif';
        } else {
            return 'https://cdn.discordapp.com/avatars/'.$this->id.'/'.$this->avatarHash.'.jpg';
        }
    }

    public function eraseCredentials()
    {
        return $this->username;
    }

    public function equals(DiscordUser $user)
    {
        return $user->getID() === $this->id;
    }

    public function serialize() {
        return serialize([
            $this->id,
            $this->username,
            $this->discriminator,
            $this->avatarHash
        ]);
    }

    public function unserialize($data) {
        list(
            $this->id,
            $this->username,
            $this->discriminator,
            $this->avatarHash
            ) = unserialize($data);
    }
}