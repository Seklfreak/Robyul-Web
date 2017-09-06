<?php

namespace RobyulWebBundle\Controller;

use MessagePack\Unpacker;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class RandomPicturesProxy extends Controller
{
    /**
     * @Route(
     *     "/static/proxy/{sourceID}/{pictureID}/{slug}.{_format}",
     *     requirements={
     *         "_format": "jpeg|jpg|gif|png",
     *         "pictureID": "\d+"
     *     }
     * )
     */
    public function proxyByNAction($sourceID, $pictureID, $slug, $_format)
    {
        $key = "robyul2-discord:randompictures:filescache:by-n:".$sourceID.":entry:".$pictureID;

        $logger = $this->get('logger');
        $logger->info("looking up \"".$key."\"");

        $redis = $this->container->get('snc_redis.default');
        $result = $redis->get($key);

        if ($result != "") {
            $unpacker = new Unpacker();
            $gdFile = $unpacker->unpack($result);

            $dlLink = 'https://drive.google.com/uc?id='.$gdFile['Id'].'&export=download';
            $logger->info("downloading \"".$dlLink."\"");

            ini_set('max_execution_time', 300);
            ini_set('default_socket_timeout', 100);
            $binary = file_get_contents($dlLink);

            return new Response(
                $binary,
                Response::HTTP_OK,
                array(
                    'Content-Type' => 'image/'.$_format,
                    'Content-Disposition' => 'inline; filename="'.$slug.'.'.$_format.'"'
                )
            );
        } else {
            throw $this->createNotFoundException('Image not found.');
        }
    }

    /**
     * @Route(
     *     "/static/proxy/{fileHash}/{slug}.{_format}",
     *     requirements={
     *         "_format": "jpeg|jpg|gif|png"
     *     }
     * )
     */
    public function proxyByHashAction($fileHash, $slug, $_format)
    {
        $key = "robyul2-discord:randompictures:filescache:by-hash:".$fileHash;

        $logger = $this->get('logger');
        $logger->info("looking up \"".$key."\"");

        $redis = $this->container->get('snc_redis.default');
        $result = $redis->get($key);

        if ($result != "") {
            $unpacker = new Unpacker();
            $gdFile = $unpacker->unpack($result);

            $dlLink = 'https://drive.google.com/uc?id='.$gdFile['Id'].'&export=download';
            $logger->info("downloading \"".$dlLink."\"");

            ini_set('max_execution_time', 300);
            ini_set('default_socket_timeout', 100);
            $binary = file_get_contents($dlLink);

            return new Response(
                $binary,
                Response::HTTP_OK,
                array(
                    'Content-Type' => 'image/'.$_format,
                    'Content-Disposition' => 'inline; filename="'.$slug.'.'.$_format.'"'
                )
            );
        } else {
            throw $this->createNotFoundException('Image not found.');
        }
    }
}
