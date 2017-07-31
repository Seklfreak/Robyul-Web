<?php

namespace RobyulWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Unirest;

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
     * @Route("/statistics")
     */
    public function statisticsAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Statistics - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "View statistics for Robyul.")
            ->addMeta('property', 'og:description', "View statistics for Robyul.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:Default:statistics.html.twig');
    }

    /**
     * @Route("/profile/backgrounds")
     */
    public function profileBackgroundsAction()
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
     * @Route("/guides/bias-roles")
     */
    public function biasRoleGuidesAction()
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Bias Roles - Guides - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "Learn how to set up Bias Role self assignment for your users.")
            ->addMeta('property', 'og:description', "Learn how to set up Bias Role self assignment for your users.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:Default:guides/biasroles.html.twig');
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

    /**
     * @Route("/ranking/{guildID}",
     *     defaults={"guildID": "global"}
     * )
     */
    public function rankingAction($guildID)
    {
        $guildName = 'Global';
        $metaName = 'View the Global Robyul Ranking.';
        $guildIcon = '';
        if ($guildID !== 'global') {
            $guildInfo = Unirest\Request::get('http://localhost:2021/guild/'.$guildID);
            $guildName = $guildInfo->body->Name;
            $guildIcon = $guildInfo->body->Icon;
            $metaName = 'View the Robyul Ranking for '.$guildName.'.';
        }

        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle($guildName." Ranking - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', $metaName)
            ->addMeta('property', 'og:description', $metaName);
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());


        $rankingInfo = Unirest\Request::get('http://localhost:2021/rankings/'.$guildID);

        return $this->render('RobyulWebBundle:Default:ranking.html.twig', array(
            'guildID' => $guildID,
            'guildName' => $guildName,
            'guildIcon' => $guildIcon,
            'rankings' => $rankingInfo->body
        ));
    }
}
