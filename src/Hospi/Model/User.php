<?php

namespace Hospi\Model;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    const ROLE_USER = 'ROLE_USER';

    private $email;
    private $password;
    private $salt;
    private $roles;
    private $createdAt;

    public function __construct($email, $password, $salt, $roles = null, $createdAt = null)
    {
        $this->email = $email;
        $this->password = $password;
        $this->salt = $salt;
        $this->roles = $roles ?: array(self::ROLE_USER);
        $this->createdAt = $createdAt ?: new \DateTime();
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
        $this->password = null;
        $this->salt = null;
    }
}
