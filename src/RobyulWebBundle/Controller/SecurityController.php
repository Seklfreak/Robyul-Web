<?php

namespace RobyulWebBundle\Controller;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use MessagePack\Packer;
use MessagePack\Unpacker;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Unirest;

class SecurityController extends Controller
{
    /**
     * @Route("/d/profile")
     */
    public function profileAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Profile - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "View your Profile.")
            ->addMeta('property', 'og:description', "View your Profile.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        $unpacker = new Unpacker();
        $packer = new Packer();
        $redis = $this->container->get('snc_redis.default');

        $key = 'robyul2-web:api:bot:guilds';
        if ($redis->exists($key) == true) {
            $allGuilds = unserialize($unpacker->unpack($redis->get($key)));
        } else {
            $allGuilds = Unirest\Request::get('http://localhost:2021/bot/guilds');
            $allGuilds = (array) $allGuilds->body;

            $redis->set($key, $packer->pack(serialize($allGuilds)));
            $redis->expireat($key, strtotime("+15 minutes"));
        }

        $isInGuilds = array();

        foreach ($allGuilds as $guild) {
            $key = 'robyul2-web:api:member:'.$guild->ID.':'.$this->getUser()->getID().':is';
            if ($redis->exists($key) == true) {
                $isMember = $unpacker->unpack($redis->get($key));
            } else {
                $isMember = Unirest\Request::get('http://localhost:2021/member/'.$guild->ID.'/'.$this->getUser()->getID().'/is');
                $isMember = (bool) $isMember->body->IsMember;

                $redis->set($key, $packer->pack($isMember));
                $redis->expireat($key, strtotime("+15 minutes"));
            }

            if ($isMember === true) {
                $isInGuilds[] = $guild;
            }
        }

        return $this->render('RobyulWebBundle:Security:profile.html.twig', array(
            'memberOfGuilds' => $isInGuilds
        ));
    }
}
