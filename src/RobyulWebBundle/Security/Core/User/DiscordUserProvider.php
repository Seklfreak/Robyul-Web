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
use RobyulWebBundle\Service\RobyulApi;

class DiscordUserProvider implements UserProviderInterface, OAuthAwareUserProviderInterface
{
    protected $robyul;

    public function __construct(RobyulApi $robyulApi)
    {
        $this->robyul = $robyulApi;
    }

    public function loadUserByUsername($id)
    {
        $data = $this->robyul->getRequest('user/' . $id);

        return new DiscordUser($id, $data["Username"], $data["Discriminator"], $data["AvatarHash"]);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        return new DiscordUser($response->getData()["id"], $response->getData()["username"], $response->getData()["discriminator"], $response->getData()["avatar"]);
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
