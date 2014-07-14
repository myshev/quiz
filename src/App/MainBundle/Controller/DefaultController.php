<?php

namespace App\MainBundle\Controller;

class DefaultController extends BaseController
{
    public function indexAction()
    {
        $vars = 'asdfasdf';
        return $this->render('MainBundle:Default:index.html.twig', [
            'vars' => $vars
        ]);
    }
}
