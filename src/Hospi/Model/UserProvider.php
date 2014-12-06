<?php

namespace Hospi\Model;

use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private $conn;

    public function __construct(Connection $connection)
    {
        $this->conn = $connection;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @see UsernameNotFoundException
     *
     * @throws UsernameNotFoundException if the user is not found
     *
     */
    public function loadUserByUsername($username)
    {
        $stmt = $this->conn->executeQuery('SELECT * FROM users WHERE email = ?', array($username));

        if (!$user = $stmt->fetch()) {
            throw new UsernameNotFoundException(sprintf('User with email "%s" does not exist.', $username));
        }

        return new User($user['email'], $user['password'], $user['salt'], explode(',', $user['roles']), new \DateTime($user['created_at']));
    }

    /**
     * @inheritdoc
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @inheritdoc
     */
    public function supportsClass($class)
    {
        return 'Hospi\Model\User' === $class;
    }
}
