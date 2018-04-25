<?php

namespace RobyulWebBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use MessagePack\Packer;
use MessagePack\Unpacker;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;

class DiscordUserProvider implements UserProviderInterface, OAuthAwareUserProviderInterface
{
    protected $redis;

    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    public function getRedisKey($id)
    {
        return 'robyul2-web:auth:user-data:'.$id;
    }

    public function loadUserByUsername($id)
    {
        $unpacker = new Unpacker();

        $userData = $unpacker->unpack($this->redis->get($this->getRedisKey($id)));

        $user = new DiscordUser($id, "", "", "");
        $user->unserialize($userData);

        return $user;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $user = new DiscordUser($response->getData()["id"], $response->getData()["username"], $response->getData()["discriminator"], $response->getData()["avatar"]);

        $packer = new Packer();

        $this->redis->set($this->getRedisKey($response->getData()["id"]), $packer->pack($user->serialize()));

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Unsupported user class "%s"', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'RobyulWebBundle\\Security\\Core\\User\\DiscordUser';
    }
}
