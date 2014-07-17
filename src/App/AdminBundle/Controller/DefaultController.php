<?php

namespace App\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $name = 'adf';
        return $this->render('AdminBundle:Default:index.html.twig', array('name' => $name));
    }
}
