<?php

namespace RobyulWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Unirest;
use MessagePack\Packer;
use MessagePack\Unpacker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use RobyulWebBundle\Service\RobyulApi;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(RobyulApi $robyulApi)
    {
        $botStatistics = $robyulApi->getRequest('statistics/bot', '+30 minutes');

        return $this->render('RobyulWebBundle:Default:index.html.twig', array(
            'botStatistics' => $botStatistics
        ));
    }

    /**
     * @Route("/commands/{guildID}",
     *     defaults={"guildID": "global"}
     * )
     */
    public function commandsAction($guildID, RobyulApi $robyulApi)
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Commands - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "View a list of all Robyul Discord Bot commands here.")
            ->addMeta('property', 'og:description', "View a list of all Robyul Discord Bot commands here.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        $guildName = 'Global';
        $guildPrefix = $this->container->getParameter('bot_default_prefix');
        $modules = null;
        $moduleNames = null;

        if ($guildID !== 'global') {
            $guildData = $robyulApi->getRequest('guild/' . $guildID);

            if (array_key_exists('Name', $guildData)) {
                $guildName = $guildData['Name'];
            }

            if (array_key_exists('BotPrefix', $guildData)) {
                $guildPrefix = $guildData['BotPrefix'];
            }

            if (array_key_exists('Features', $guildData) &&
                array_key_exists('Modules', $guildData['Features'])) {
                $modules = (array)(((array)$guildData['Features'])['Modules']);
            }
        }

        if ($modules !== null && is_array($modules)) {
            $moduleNames = array();
            foreach ($modules as $module) {
                if (array_key_exists('Name', $module)) {
                    $moduleNames[] = ((array)$module)['Name'];
                }
            }
        }

        return $this->render('RobyulWebBundle:Default:commands.html.twig',
            array(
                'guildName' => $guildName,
                'guildBotPrefix' => $guildPrefix,
                'moduleNames' => $moduleNames
            ));
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
    public function profileBackgroundsAction(RobyulApi $robyulApi)
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Profile Backgrounds - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "Customize your Robyul Discord Bot Profile using Background Pictures.")
            ->addMeta('property', 'og:description', "Customize your Robyul Discord Bot Profile using Background Pictures.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        $backgrounds = $robyulApi->getRequest('/backgrounds', '', true);

        $tags = array();
        foreach ($backgrounds as $background) {
            if (array_key_exists('Tags', $background)) {
                foreach ($background->Tags as $tag) {
                    if (!in_array($tag, $tags)) {
                        $tags[] = $tag;
                    }
                }
            }
        }

        return $this->render('RobyulWebBundle:Default:profileBackgrounds.html.twig', array(
            'backgrounds' => $backgrounds,
            'tags' => $tags
        ));
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
    public function rankingAction($guildID, RobyulApi $robyulApi)
    {
        $guildName = 'Global';
        $metaName = 'View the Global Robyul Ranking.';
        $guildIcon = '';

        if ($guildID !== 'global') {
            $guildData = $robyulApi->getRequest('guild/' . $guildID);

            $guildName = $guildData['Name'];
            $guildIcon = $guildData['Icon'];
            $metaName = 'View the Robyul Ranking for ' . $guildName . '.';
        }

        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle($guildName . " Ranking - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', $metaName)
            ->addMeta('property', 'og:description', $metaName);
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());


        $rankingData = $robyulApi->getRequest('rankings/' . $guildID, '+30 minutes');

        return $this->render('RobyulWebBundle:Default:ranking.html.twig', array(
            'guildID' => $guildID,
            'guildName' => $guildName,
            'guildIcon' => $guildIcon,
            'rankings' => $rankingData
        ));
    }

    /**
     * @Route("/embed-creator")
     */
    public function embedAction(RobyulApi $robyulApi)
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Embed Creator - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "Create pretty Discord Embeds with ease using Robyul.")
            ->addMeta('property', 'og:description', "w.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        $botData = $robyulApi->getRequest('user/@me');

        $botID = $botData['ID'];
        $botUsername = $botData['Username'];
        $botAvatarHash = $botData['AvatarHash'];

        return $this->render('RobyulWebBundle:Default:embed.html.twig', array(
            'botID' => $botID,
            'botUsername' => $botUsername,
            'botAvatarHash' => $botAvatarHash,
        ));
    }

    /**
     * @Route("/invite")
     */
    public function inviteAction()
    {
        return $this->redirect($this->getParameter('bot_invite_link'));
    }

    /**
     * @Route("/session")
     * @Method({"POST"})
     */
    public function sessionAction(Request $request)
    {
        if (!$request->hasPreviousSession() || $request->getSession()->getId() == "") {
            return;
        }

        return new Response(
            $request->getSession()->getId(),
            Response::HTTP_OK,
            array('content-type' => 'text/plain')
        );
    }
}
