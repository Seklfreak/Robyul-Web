<?php

namespace RobyulWebBundle\Controller;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
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

        dump($this->getUser());

        $allGuilds = Unirest\Request::get('http://localhost:2021/bot/guilds');
        $isInGuilds = array();

        foreach ($allGuilds->body as $guild) {
            $member = Unirest\Request::get('http://localhost:2021/member/'.$guild->ID.'/'.$this->getUser()->getID().'/is');
            if ($member->body->IsMember === true) {
                $isInGuilds[] = $guild;
            }
        }

        return $this->render('RobyulWebBundle:Security:profile.html.twig', array(
            'memberOfGuilds' => $isInGuilds
        ));
    }
}
