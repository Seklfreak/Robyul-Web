<?php

namespace RobyulWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('RobyulWebBundle:Default:index.html.twig');
    }

    /**
     * @Route("/commands")
     */
    public function commandsAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Commands - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "View a list of all Robyul Discord Bot commands here.")
            ->addMeta('property', 'og:description', "View a list of all Robyul Discord Bot commands here.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:Default:commands.html.twig');
    }

    /**
     * @Route("/profile/backgrounds")
     */
    public function profileBackgrounds()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Profile Backgrounds - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "Customize your Robyul Discord Bot Profile using Background Pictures.")
            ->addMeta('property', 'og:description', "Customize your Robyul Discord Bot Profile using Background Pictures.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        $conn = \r\connect('localhost', 28015, $this->container->getParameter('rethinkdb_database'));

        $backgroundsIterator = \r\table("profile_backgrounds")->run($conn);

        $backgrounds = array();
        foreach ($backgroundsIterator as $backgroundIterator) {
            $backgrounds[$backgroundIterator["id"]] = iterator_to_array($backgroundIterator);
        }
        ksort($backgrounds, SORT_STRING|SORT_FLAG_CASE);

        return $this->render('RobyulWebBundle:Default:profileBackgrounds.html.twig',
            array('backgrounds' => $backgrounds));
    }

    /**
     * @Route("/imprint")
     */
    public function imprintAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Impressum - The KPop Discord Bot - Robyul");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:Default:imprint.html.twig');
    }

    /**
     * @Route("/privacy-policy")
     */
    public function privacyPolicyAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Datenschutzerklärung - The KPop Discord Bot - Robyul");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:Default:privacyPolicy.html.twig');
    }
}
