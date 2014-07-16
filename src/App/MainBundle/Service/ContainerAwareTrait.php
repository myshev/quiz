<?php

namespace App\MainBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

trait ContainerAwareTrait
{
    /**
     * @return ContainerInterface
     */
    abstract public function getContainer();
}




















