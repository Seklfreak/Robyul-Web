<?php

namespace RobyulWebBundle\Controller;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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

        return $this->render('RobyulWebBundle:Security:profile.html.twig');
    }
}
