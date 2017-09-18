<?php

namespace RobyulWebBundle\EventSubscriber;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use MessagePack\Packer;

class RobyulSessionSubscriber implements EventSubscriberInterface
{

    private $securityTokenStorage;
    private $container;
    
    public function __construct(TokenStorageInterface $securityTokenStorage, ContainerInterface $container) {
        $this->securityTokenStorage = $securityTokenStorage;
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return array(
           KernelEvents::REQUEST => array(
               array('onKernelRequest', -100),
           )
        );
    }
    
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession() || $request->getSession()->getId() == "") {
            return;
        }
        

        $data = array(
            'DiscordUserID' => "",
        );

        if ($this->securityTokenStorage->getToken() != null) {
            $userID = $this->securityTokenStorage->getToken()->getUser()->getID();
            $data['DiscordUserID'] = $userID;
        }

        $key = 'robyul2-web:robyul-session:' . $request->getSession()->getId();

        $packer = new Packer();
        $redis = $this->container->get('snc_redis.default');
        $redis->set($key, $packer->pack($data));
        $redis->expireat($key, strtotime("+30 minutes"));
    }
}
