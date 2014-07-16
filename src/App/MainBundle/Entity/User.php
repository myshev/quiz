<?php
namespace App\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\MainBundle\Entity\UserRepository")
 * @ORM\Table(name="user")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(name="username", type="string", unique=false, nullable=true)
     */
    protected $username;

    /**
     * @ORM\Column(name="is_enabled", type="boolean")
     */
    protected $enabled = false;

    /**
     * @ORM\Column(name="roles", type="roles")
     */
    protected $roles = [];

    /**
     * @ORM\Column(name="password", type="string")
     */
    protected $password = '';

    protected $plainPassword = '';

    /**
     * @ORM\Column(type="string")
     */
    protected $salt = '';

    /**
     * @ORM\Column(name="create_date", type="datetime")
     */
    protected $createDate;

    public function __construct() {
        $this->createDate = new \DateTime();
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setEmail($email)
    {
        $this->email = (string) $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setUsername($username)
    {
        $this->username = (string) $username;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = (bool) $enabled;

        return $this;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_values(array_unique($roles));
    }

    public function setRoles(array $roles)
    {
        $this->roles = [];
        foreach ($roles as $role) {
            $this->roles[] = $role;
        }
        return $this;
    }

    public function getSalt()
    {
        $this->salt;
    }

    public function setPassword($password)
    {
        $this->password = (string) $password;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = '';
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = (string) $plainPassword;

        return $this;
    }
}