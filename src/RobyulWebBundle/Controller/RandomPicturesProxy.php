<?php

namespace RobyulWebBundle\Controller;

use MessagePack\Unpacker;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use RobyulWebBundle\Service\RobyulApi;

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
        $key = "robyul2-discord:randompictures:filescache:by-n:" . $sourceID . ":entry:" . $pictureID;

        $logger = $this->get('logger');
        $logger->info("looking up \"" . $key . "\"");

        $redis = $this->container->get('snc_redis.default');
        $result = $redis->get($key);

        if ($result != "") {
            $key = "robyul2-discord:randompictures:filescache:by-hash:" . $result;
            $result = $redis->get($key);
            if ($result != "") {
                $unpacker = new Unpacker();
                $gdFile = $unpacker->unpack($result);

                $dlLink = 'https://drive.google.com/uc?id=' . $gdFile['Id'] . '&export=download';
                $logger->info("downloading \"" . $dlLink . "\"");

                ini_set('max_execution_time', 300);
                ini_set('default_socket_timeout', 100);
                $binary = file_get_contents($dlLink);

                return new Response(
                    $binary,
                    Response::HTTP_OK,
                    array(
                        'Content-Type' => 'image/' . $_format,
                        'Content-Disposition' => 'inline; filename="' . $slug . '.' . $_format . '"'
                    )
                );
            }
        }
        throw $this->createNotFoundException('Image not found.');
    }

    /**
     * @Route(
     *     "/static/proxy/{fileHash}/{slug}.{_format}",
     *     requirements={
     *         "_format": "jpeg|jpg|gif|png"
     *     }
     * )
     */
    public function proxyByHashAction(RobyulApi $robyulApi, $fileHash, $slug, $_format)
    {
        // try random pictures redis cache
        $key = "robyul2-discord:randompictures:filescache:by-hash:" . $fileHash;

        $logger = $this->get('logger');
        $logger->info("looking up \"" . $key . "\"");

        $redis = $this->container->get('snc_redis.default');
        $result = $redis->get($key);

        if ($result != "") {
            $unpacker = new Unpacker();
            $gdFile = $unpacker->unpack($result);

            $dlLink = 'https://drive.google.com/uc?id=' . $gdFile['Id'] . '&export=download';
            $logger->info("downloading \"" . $dlLink . "\"");

            ini_set('max_execution_time', 300);
            ini_set('default_socket_timeout', 100);
            $binary = file_get_contents($dlLink);

            return new Response(
                $binary,
                Response::HTTP_OK,
                array(
                    'Content-Type' => 'image/' . $_format,
                    'Content-Disposition' => 'inline; filename="' . $slug . '.' . $_format . '"'
                )
            );
        } else {
            // try robyul file retrieval
            $fileData = $robyulApi->getRequest('file/' . $fileHash, '');

            if (!is_array($fileData)) {
                throw $this->createNotFoundException('Image not found.');
            }

            $data = '';
            $filetype = '';

            if (array_key_exists('Data', $fileData)) {
                $data = base64_decode($fileData['Data']);
            }
            if (array_key_exists('FileName', $fileData)) {
                $filetype = $fileData['FileName'];
            }

            if ($data !== '' && $data !== false) {
                return new Response(
                    $data,
                    Response::HTTP_OK,
                    array(
                        'Content-Type' => $filetype,
                        'Content-Disposition' => 'inline; filename="' . $slug . '.' . $_format . '"'
                    )
                );
            }

            throw $this->createNotFoundException('Image not found.');
        }
    }
}
