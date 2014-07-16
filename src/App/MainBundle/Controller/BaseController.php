<?php

namespace App\MainBundle\Controller;

use App\MainBundle\Service\ContainerTrait;
use App\MainBundle\Service\DoctrineTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller {

    use ContainerTrait;
    use DoctrineTrait;

    public function getContainer()
    {
        return $this->container;
    }
}