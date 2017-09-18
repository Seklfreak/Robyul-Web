<?php

namespace RobyulWebBundle\Controller;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Unirest;
use Symfony\Component\HttpFoundation\Response;
use MessagePack\Packer;
use MessagePack\Unpacker;

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
        $unpacker = new Unpacker();
        $packer = new Packer();
        $redis = $this->container->get('snc_redis.default');

        $key = 'robyul2-web:api:profile:'.$guildID.':'.$userID;;
        if ($redis->exists($key) == true) {
            $profileData = $unpacker->unpack($redis->get($key));
        } else {
            $profile = Unirest\Request::get('http://localhost:2021/profile/'.$userID.'/'.$guildID, array('Authorization' => 'Webkey '.$this->getParameter('bot_webkey')));
            $profileData = $profile->raw_body;

            $redis->set($key, $packer->pack($profileData));
            $redis->expireat($key, strtotime("+15 minutes"));
        }

        return new Response($profileData);
    }
}
