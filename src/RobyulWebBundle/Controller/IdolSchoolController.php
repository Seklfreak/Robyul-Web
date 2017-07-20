<?php

namespace RobyulWebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class IdolSchoolController extends Controller
{
    /**
     * @Route("/idolschool/")
     */
    public function indexAction()
    {
        return $this->render('RobyulWebBundle:IdolSchool:index.html.twig');
    }
}
