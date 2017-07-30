<?php

namespace RobyulWebBundle\Controller;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Unirest;
use Symfony\Component\HttpFoundation\Response;

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
    public function profileAction($userID, $guildID)
    {
        $profile = Unirest\Request::get('http://localhost:2021/profile/'.$userID.'/'.$guildID);

        return new Response($profile->raw_body);
    }
}
