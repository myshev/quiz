<?php

namespace App\MainBundle\Service;

use App\MainBundle\Entity\User;
use App\MainBundle\Security\UserProvider;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Security\Core\SecurityContext;
use Knp\Component\Pager\Paginator;

trait ContainerTrait
{
    use ContainerAwareTrait;

    /**
     * @return User
     * @throws \LogicException
     */
    public function getUser()
    {
        if (null === $token = $this->getSecurityContext()->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }

    /**
     * @return SecurityContext
     */
    public function getSecurityContext()
    {
        return $this->getContainer()->get('security.context');
    }

    /**
     * @return FormFactory
     */
    public function getFormFactory()
    {
        return $this->getContainer()->get('form.factory');
    }

    /**
     * @return UserProvider
     */
    public function getUserProvider()
    {
        return $this->getContainer()->get('quiz.security.user_provider');
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->getContainer()->get('twig');
    }

    /**
     * @return EncoderFactory
     */
    public function getEncoderFactory()
    {
        return $this->getContainer()->get('security.encoder_factory');
    }

    /**
     * @return Paginator
     */
    public function getPaginator()
    {
        return $this->getContainer()->get('knp_paginator');
    }
}