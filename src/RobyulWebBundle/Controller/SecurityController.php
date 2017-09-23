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
            $allGuilds = Unirest\Request::get('http://localhost:2021/bot/guilds', array('Authorization' => 'Webkey '.$this->getParameter('bot_webkey')));
            $allGuilds = (array) $allGuilds->body;

            $redis->set($key, $packer->pack(serialize($allGuilds)));
            $redis->expireat($key, strtotime("+15 minutes"));
        }

        $isInGuilds = array();
        $adminInGuilds = array();
        $modInGulds = array();

        foreach ($allGuilds as $guild) {
            $key = 'robyul2-web:api:member:'.$guild->ID.':'.$this->getUser()->getID().':is';
            if ($redis->exists($key) == true) {
                $isMember = $unpacker->unpack($redis->get($key));
            } else {
                $isMember = Unirest\Request::get('http://localhost:2021/member/'.$guild->ID.'/'.$this->getUser()->getID().'/is', array('Authorization' => 'Webkey '.$this->getParameter('bot_webkey')));
                $isMember = (bool) $isMember->body->IsMember;

                $redis->set($key, $packer->pack($isMember));
                $redis->expireat($key, strtotime("+15 minutes"));
            }

            if ($isMember === true) {
                $isInGuilds[] = $guild;

                $key = 'robyul2-web:api:member:'.$guild->ID.':'.$this->getUser()->getID().':status';
                if ($redis->exists($key) == true) {
                    $statusMember = $unpacker->unpack($redis->get($key));
                } else {
                    $statusMember = Unirest\Request::get('http://localhost:2021/member/'.$guild->ID.'/'.$this->getUser()->getID().'/status', array('Authorization' => 'Webkey '.$this->getParameter('bot_webkey')));
                    $statusMember = (array) $statusMember->body;

                    $redis->set($key, $packer->pack($statusMember));
                    $redis->expireat($key, strtotime("+15 minutes"));
                }
                $isAdmin = (bool) $statusMember['IsGuildAdmin'];
                $isMod = (bool) $statusMember['IsGuildMod'];

                if ($isAdmin === true) {
                    $adminInGuilds[] = $guild->ID;
                }
                if ($isMod === true) {
                    $modInGulds[] = $guild->ID;
                }
            }
        }

        return $this->render('RobyulWebBundle:Security:profile.html.twig', array(
            'memberOfGuilds' => $isInGuilds,
            'adminOfGuildIDs' => $adminInGuilds,
            'modOfGuildIDs' => $modInGulds,
        ));
    }

    /**
     * @Route("/d/randompictures/{guildID}")
     */
    public function randomPicturesHistoryAction($guildID)
    {
        $unpacker = new Unpacker();
        $packer = new Packer();
        $redis = $this->container->get('snc_redis.default');

        $key = 'robyul2-web:api:guild:' . $guildID;
        if ($redis->exists($key) == true) {
            $guildData = unserialize($unpacker->unpack($redis->get($key)));
        } else {
            $guildInfo = Unirest\Request::get('http://localhost:2021/guild/' . $guildID, array('Authorization' => 'Webkey '.$this->getParameter('bot_webkey')));
            $guildData = (array)$guildInfo->body;

            $redis->set($key, $packer->pack(serialize($guildData)));
            $redis->expireat($key, strtotime("+1 hour"));
        }

        $key = 'robyul2-web:api:member:'.$guildID.':'.$this->getUser()->getID().':is';
        if ($redis->exists($key) == true) {
            $isMember = $unpacker->unpack($redis->get($key));
        } else {
            $isMember = Unirest\Request::get('http://localhost:2021/member/'.$guildID.'/'.$this->getUser()->getID().'/is', array('Authorization' => 'Webkey '.$this->getParameter('bot_webkey')));
            $isMember = (bool) $isMember->body->IsMember;

            $redis->set($key, $packer->pack($isMember));
            $redis->expireat($key, strtotime("+15 minutes"));
        }

        if (!$isMember) {
            return $this->redirectToRoute('robyulweb_security_profile');
        }

        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle($guildData['Name'] . " Picture History - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "View the most recent pictures for " . $guildData['Name'] . ".")
            ->addMeta('property', 'og:description', "View the most recent pictures for " . $guildData['Name'] . ".");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        $pictureHistoryInfo = Unirest\Request::get('http://localhost:2021/randompictures/history/' . $guildID . '/1/100', array('Authorization' => 'Webkey '.$this->getParameter('bot_webkey')));
        $pictureHistoryData = (array)$pictureHistoryInfo->body;

        return $this->render('RobyulWebBundle:Security:randomPicturesHistory.html.twig', array(
            'pictureHistoryItems' => $pictureHistoryData
        ));
    }
    
        /**
         * @Route("/d/statistics/{guildID}")
         */
        public function statisticsAction($guildID)
        {
            $statusMember = Unirest\Request::get('http://localhost:2021/member/'.$guildID.'/'.$this->getUser()->getID().'/status', array('Authorization' => 'Webkey '.$this->getParameter('bot_webkey')));
            $statusMember = (array) $statusMember->body;

            $packer = new Packer();
            $redis = $this->container->get('snc_redis.default');
            $key = 'robyul2-web:api:member:'.$guildID.':'.$this->getUser()->getID().':status';
            $redis->set($key, $packer->pack($statusMember));
            $redis->expireat($key, strtotime("+15 minutes"));

            if ($statusMember['IsGuildAdmin'] === false && $statusMember['IsGuildMod'] === false) {
                return $this->redirectToRoute('robyulweb_security_profile');
            }

            return $this->render('RobyulWebBundle:Security:statistics.html.twig', array(
                'guildID' => $guildID,
            ));
        }
}
