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
     * @Route("/{vanityName}")
     */
    public function vanityInviteAction($vanityName, RobyulApi $robyulApi)
    {
        $vanityInviteData = $robyulApi->getRequest('vanityinvite/' . $vanityName, '');

        if (array_key_exists('Code', $vanityInviteData)) {
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
