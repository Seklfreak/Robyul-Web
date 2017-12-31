<?php

namespace RobyulWebBundle\VanityInviteController;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Unirest;
use MessagePack\Packer;
use MessagePack\Unpacker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use RobyulWebBundle\Service\RobyulApi;

class VanityInviteController extends Controller
{
    /**
     * @Route("/{vanityName}", requirements={"vanityName"=".+"})
     */
    public function vanityInviteAction($vanityName, RobyulApi $robyulApi, Request $request)
    {
        $vanityInviteData = $robyulApi->getRequest(
            'vanityinvite/' . $vanityName . '?referer=' . urlencode($request->headers->get('referer', '')),
            '');

        if (array_key_exists('Code', $vanityInviteData)) {
            $userIp = $request->getClientIp();
            if ($request->headers->get('HTTP_CF_CONNECTING_IP') !== null) {
                $userIp = $request->headers->get('HTTP_CF_CONNECTING_IP');
            }
            $clientId = md5($userIp . '-' . $request->headers->get('user-agent'));

            $guildIdText = '';
            if (array_key_exists('GuildID', $vanityInviteData)) {
                $guildIdText = 'GuildID:' . $vanityInviteData['GuildID'];
            }

            $this->get('gamp.analytics')
                ->setClientId($clientId)
                ->setDocumentPath('/' . $vanityName)
                ->setUserAgentOverride($request->headers->get('user-agent', ''))
                ->setDocumentReferrer($request->headers->get('referer', ''))
                ->setUserLanguage($request->headers->get('accept-language', ''))
                ->setIpOverride($userIp)
                ->setDataSource('php')
                ->setGeographicalOverride($request->headers->get('HTTP_CF_IPCOUNTRY', 'XX'))
                ->setCampaignName('vanityinvite')
                ->setCampaignSource('web')
                ->setCampaignMedium('discord')
                ->setCampaignContent($guildIdText)
                //->sendEvent();
                ->sendPageview();

            return $this->redirect($this->getParameter('discord_invite_base') . '/' . $vanityInviteData['Code']);
        }

        return $this->redirectToRoute('robyulweb_default_index');
    }

    /**
     * @Route("/")
     */
    public function vanityInviteIndexAction()
    {
        return $this->redirectToRoute('robyulweb_default_index');
    }
}
