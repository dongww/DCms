<?php
namespace Provider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Core\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * 处理用户的 Silex Provider
 *
 * Class UserProvider
 * @package Provider\Provider
 */
class UserProvider implements UserProviderInterface
{
    public function __construct()
    {
    }

    public function loadUserByUsername($username)
    {
        $user = \R::findOne('user', ' username = ? ', array($username));

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('用户名 "%s" 不存在', $username));
        }

        return new User($user['id'], $user['username'], $user['password'], explode(',', $user['roles']), true, true, true, true);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}