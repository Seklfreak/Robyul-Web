<?php

namespace RobyulWebBundle\Controller;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use RobyulWebBundle\Service\RobyulApi;

class FrameController extends Controller
{
    /**
     * @Route("/frame/profile/{userID}/{guildID}",
     *     defaults={"guildID": "global"},
     *     requirements={
     *         "userID": "\d+"
     *     }
     * )
     */
    public function profileAction($userID, $guildID, RobyulApi $robyulApi)
    {
        $profileData = $robyulApi->getRequestRaw('profile/'.$userID.'/'.$guildID, '+15 minutes');

        return new Response($profileData);
    }
}
