<?php

namespace RobyulWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use danielmewes\PhpRql\rdb\rdb as r;

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
        return $this->render('RobyulWebBundle:Default:commands.html.twig');
    }

    /**
     * @Route("/profile/backgrounds")
     */
    public function profileBackgrounds()
    {
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
     * @Route("/imprint")
     */
    public function imprintAction()
    {
        return $this->render('RobyulWebBundle:Default:imprint.html.twig');
    }

    /**
     * @Route("/privacy-policy")
     */
    public function privacyPolicyAction()
    {
        return $this->render('RobyulWebBundle:Default:privacyPolicy.html.twig');
    }
}
