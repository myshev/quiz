<?php

namespace App\AdminBundle\Twig;

use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables as BaseGlobalVariables;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GlobalVariables extends BaseGlobalVariables
{
    protected $breadcrumbs;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->breadcrumbs = new Breadcrumbs();
    }

    public function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }
}
