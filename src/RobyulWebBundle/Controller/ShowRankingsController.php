<?php

namespace RobyulWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ShowRankingsController extends Controller
{
    /**
     * @Route("/idolschool/")
     */
    public function indexAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Idol School (fromis_) Ranking - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "View the Ranking over time for Mnet's Idol School forming the Girl Group fromis_.")
            ->addMeta('property', 'og:description', "View the Ranking over time for Mnet's Idol School forming the Girl Group fromis_.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:ShowRankings:index.html.twig');
    }

    /**
     * @Route("/mixnine/girls/")
     */
    public function mixnineAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("MIXNINE Ranking Girls - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "View the Girls Ranking over time for YG's MIXNINE survival show.")
            ->addMeta('property', 'og:description', "View the Girls Ranking over time for YG's MIXNINE survival show.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:ShowRankings:mixnine.html.twig');
    }

    /**
     * @Route("/mixnine/boys/")
     */
    public function mixnineBoysAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("MIXNINE Ranking Boys - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "View the Boys Ranking over time for YG's MIXNINE survival show.")
            ->addMeta('property', 'og:description', "View the Boys Ranking over time for YG's MIXNINE survival show.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:ShowRankings:mixnine_boys.html.twig');
    }

    /**
     * @Route("/theunit/boys/")
     */
    public function theunitBoysAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("The Unit Ranking Boys - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "Check out the Boys Ranking for KBS's The Unit survival show.")
            ->addMeta('property', 'og:description', "Check out the Boys Ranking for KBS's The Unit survival show.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:ShowRankings:theunit_boys.html.twig');
    }

    /**
     * @Route("/theunit/girls/")
     */
    public function theunitGirlsAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("The Unit Ranking Girls - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "Check out the Girls Ranking for KBS's The Unit survival show.")
            ->addMeta('property', 'og:description', "Check out the Girls Ranking for KBS's The Unit survival show.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:ShowRankings:theunit_girls.html.twig');
    }
}
