<?php

namespace App\MainBundle\Security;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    protected $em;
    protected $class = 'App\MainBundle\Entity\User';

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function loadUserByUsername($username)
    {
        if (false !== strpos($username, '@')) {
            $user = $this->findBy([
                'email' => $username
            ]);
        } else {
            $user = $this->findBy([
                'username' => $username
            ]);
        }

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', $this->class, get_class($user)));
        }

        if (null === $reloadedUser = $this->findBy(['id' => $user->getId()])) {
            throw new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    protected function findBy($params)
    {
        return $this->em->getRepository($this->class)->findOneBy($params);
    }

    public function supportsClass($class)
    {
        return $this->class === $class;
    }
}