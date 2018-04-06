<?php

namespace RobyulWebBundle\Controller;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use MessagePack\Packer;
use MessagePack\Unpacker;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Unirest;
use RobyulWebBundle\Service\RobyulApi;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    /**
     * @Route("/d/profile")
     */
    public function profileAction(Request $request, RobyulApi $robyulApi)
    {
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle("Profile - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "View your Profile.")
            ->addMeta('property', 'og:description', "View your Profile.");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        // refresh button (invalidates rest cache)
        $refreshForm = $this->createFormBuilder()
            ->add('refresh', SubmitType::class, array(
                'label' => 'Refresh',
                'attr' => array(
                    'class' => 'btn btn-secondary btn-sm'
                )
            ))
            ->getForm();
        $refreshForm->handleRequest($request);
        if ($refreshForm->isSubmitted() && $refreshForm->isValid()) {
            $robyulApi->invalidate('user/' . $this->getUser()->getID() . '/guilds');
            $robyulApi->invalidate('rankings/user/' . $this->getUser()->getID() . '/all');
            return $this->redirectToRoute('robyulweb_security_profile');
        }

        $allGuilds = $robyulApi->getRequest('user/' . $this->getUser()->getID() . '/guilds', '+15 minutes');

        $allRankings = $robyulApi->getRequest('rankings/user/' . $this->getUser()->getID() . '/all', '+15 minutes');

        $adjustedRankings = array();
        $globalAdjustedEXP = 0;
        $i = 0;
        foreach ($allRankings as $ranking) {
            if ($ranking->GuildID != "global" && $ranking->Level > 0) {
                $globalAdjustedEXP += $ranking->EXP;
            }
            $adjustedRankings[] = $ranking;
            $i++;
            if ($i > 10) {
                break;
            }
        }

        foreach ($allRankings as $ranking) {
            $ranking->GuildName = 'N/A';
            if ($ranking->GuildID == 'global') {
                $ranking->GuildName = 'Global';
            } else {
                foreach ($allGuilds as $guild) {
                    if ($ranking->GuildID == $guild->ID) {
                        $ranking->GuildName = $guild->Name;
                        break;
                    }
                }
            }
        }

        return $this->render('RobyulWebBundle:Security:profile.html.twig', array(
            'memberOfGuilds' => $allGuilds,
            'allRankings' => $adjustedRankings,
            'globalAdjustedEXP' => $globalAdjustedEXP,
            'rawRankings' => $allRankings,
            'refreshForm' => $refreshForm->createView()
        ));
    }

    /**
     * @Route("/d/randompictures/{guildID}")
     */
    public function randomPicturesHistoryAction($guildID, RobyulApi $robyulApi)
    {
        $memberStatus = $robyulApi->getRequest('member/' . $guildID . '/' . $this->getUser()->getID() . '/is', '+15 minutes');

        if ($memberStatus['IsMember'] !== true) {
            return $this->redirectToRoute('robyulweb_security_profile');
        }

        $guildData = $robyulApi->getRequest('guild/' . $guildID);

        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle($guildData['Name'] . " Picture History - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "View the most recent pictures for " . $guildData['Name'] . ".")
            ->addMeta('property', 'og:description', "View the most recent pictures for " . $guildData['Name'] . ".");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        $pictureHistoryData = $robyulApi->getRequest('randompictures/history/' . $guildID . '/1/100', '+1 minutes');

        return $this->render('RobyulWebBundle:Security:randomPicturesHistory.html.twig', array(
            'pictureHistoryItems' => $pictureHistoryData
        ));
    }

    /**
     * @Route("/d/statistics/{guildID}")
     */
    public function statisticsAction($guildID, RobyulApi $robyulApi)
    {
        $statusMember = $robyulApi->getRequest('member/' . $guildID . '/' . $this->getUser()->getID() . '/status', '+1 minutes');

        if ($statusMember['IsGuildAdmin'] !== true && $statusMember['IsGuildMod'] !== true) {
            return $this->redirectToRoute('robyulweb_security_profile');
        }

        $guildData = $robyulApi->getRequest('guild/' . $guildID);
        $guildName = $guildData['Name'];
        $guildIcon = $guildData['Icon'];

        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle($guildName . " Server Statistics - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "View the server statistics for " . $guildName . ".")
            ->addMeta('property', 'og:description', "View the server statistics for " . $guildName . ".");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:Security:statistics.html.twig', array(
            'guildID' => $guildID,
            'guildName' => $guildName,
            'guildIcon' => $guildIcon,
        ));
    }

    /**
     * @Route("/d/chatlog/{guildID}")
     */
    public function chatlogAction($guildID, RobyulApi $robyulApi)
    {
        $statusMember = $robyulApi->getRequest('member/' . $guildID . '/' . $this->getUser()->getID() . '/status', '+1 minutes');

        if ($statusMember['HasGuildPermissionAdministrator'] !== true) {
            return $this->redirectToRoute('robyulweb_security_profile');
        }

        $guildData = $robyulApi->getRequest('guild/' . $guildID, '+1 minutes');

        $guildName = $guildData['Name'];
        $guildIcon = $guildData['Icon'];
        $guildChannels = $guildData['Channels'];
        $chatlogEnabled = (bool)$guildData['Features']->Chatlog->Enabled;
        if ($chatlogEnabled !== true) {
            return $this->redirectToRoute('robyulweb_security_profile');
        }

        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle($guildName . " Chatlog - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "View the chatlog for " . $guildName . ".")
            ->addMeta('property', 'og:description', "View the chatlog for " . $guildName . ".");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:Security:chatlog.html.twig', array(
            'guildID' => $guildID,
            'guildName' => $guildName,
            'guildIcon' => $guildIcon,
            'guildChannels' => $guildChannels,
        ));
    }

    /**
     * @Route("/d/vanityinvite/{guildID}")
     */
    public function vanityInviteAction($guildID, RobyulApi $robyulApi)
    {
        $statusMember = $robyulApi->getRequest('member/' . $guildID . '/' . $this->getUser()->getID() . '/status', '+1 minutes');

        if ($statusMember['IsGuildAdmin'] !== true && $statusMember['IsGuildMod'] !== true) {
            return $this->redirectToRoute('robyulweb_security_profile');
        }

        $guildData = $robyulApi->getRequest('guild/' . $guildID, '+1 minutes');

        $guildName = $guildData['Name'];
        $guildIcon = $guildData['Icon'];
        $vanityInviteName = (string)$guildData['Features']->VanityInvite->VanityInviteName;
        if (strlen($vanityInviteName) <= 0) {
            return $this->redirectToRoute('robyulweb_security_profile');
        }

        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle($guildName . " Custom Invite - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "View statistics for the custom invite for " . $guildName . ".")
            ->addMeta('property', 'og:description', "View statistics for the custom invite for " . $guildName . ".");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:Security:vanityinvite.html.twig', array(
            'guildID' => $guildID,
            'guildName' => $guildName,
            'guildIcon' => $guildIcon,
            'vanityInviteName' => $vanityInviteName,
        ));
    }

    /**
     * @Route("/d/eventlog/{guildID}")
     */
    public function eventlogAction($guildID, RobyulApi $robyulApi)
    {
        $statusMember = $robyulApi->getRequest('member/' . $guildID . '/' . $this->getUser()->getID() . '/status', '+1 minutes');

        if ($statusMember['IsGuildMod'] !== true && $statusMember['IsGuildAdmin'] !== true) {
            return $this->redirectToRoute('robyulweb_security_profile');
        }

        $guildData = $robyulApi->getRequest('guild/' . $guildID, '+1 minutes');

        $guildName = $guildData['Name'];
        $guildIcon = $guildData['Icon'];
        $guildChannels = $guildData['Channels'];
        $eventlogEnabled = (bool)$guildData['Features']->Eventlog->Enabled;
        if ($eventlogEnabled !== true) {
            return $this->redirectToRoute('robyulweb_security_profile');
        }

        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle($guildName . " Eventlog - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "View the eventlog for " . $guildName . ".")
            ->addMeta('property', 'og:description', "View the eventlog for " . $guildName . ".");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:Security:eventlog.html.twig', array(
            'guildID' => $guildID,
            'guildName' => $guildName,
            'guildIcon' => $guildIcon,
            'guildChannels' => $guildChannels,
        ));
    }

    /**
     * @Route("/d/settings/{guildID}")
     */
    public function settingsAction($guildID, RobyulApi $robyulApi)
    {
        $statusMember = $robyulApi->getRequest('member/' . $guildID . '/' . $this->getUser()->getID() . '/status', '+1 minutes');

        if ($statusMember['IsGuildMod'] !== true && $statusMember['IsGuildAdmin'] !== true) {
            return $this->redirectToRoute('robyulweb_security_profile');
        }

        $guildData = $robyulApi->getRequest('guild/' . $guildID, '+1 minutes');

        $guildName = $guildData['Name'];
        $guildIcon = $guildData['Icon'];
        $guildChannels = $guildData['Channels'];

        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage
            ->setTitle($guildName . " Settings - The KPop Discord Bot - Robyul")
            ->addMeta('name', 'description', "Change Robyul Settings for " . $guildName . ".")
            ->addMeta('property', 'og:description', "Change Robyul Settings for " . $guildName . ".");
        $seoPage->addMeta('property', 'og:title', $seoPage->getTitle());

        return $this->render('RobyulWebBundle:Security:settings.html.twig', array(
            'guildID' => $guildID,
            'guildName' => $guildName,
            'guildIcon' => $guildIcon,
            'guildChannels' => $guildChannels,
        ));
    }
}
