<?php
/**
 * File UserProvider.php
 */

namespace Nerdery\Xbox\Bundle\UserBundle\Security\Provider;

use Nerdery\Xbox\Bundle\UserBundle\Builder\UserBuilder;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 *
 * Provides a Nerdery user object to the authenticator
 *
 * @author Nate Brunette <nate@b7interactive.com>
 * @package Nerdery\Xbox\Bundle\UserBundle\Security\Provider
 */
class UserProvider implements UserProviderInterface
{
    /**
     * User builder
     *
     * @var UserBuilder
     */
    private $userBuilder;

    /**
     * Constructor
     *
     * @param UserBuilder $userBuilder
     */
    public function __construct(UserBuilder $userBuilder)
    {
        $this->userBuilder = $userBuilder;
    }

    /**
     * Get the user builder
     *
     * The user builder may also be passed around through the dependency injector, but
     * I think it's clearer to fetch it from this object. That way you can be sure you're
     * using the correct object.
     *
     * @return UserBuilder
     */
    public function getUserBuilder()
    {
        return $this->userBuilder;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * Because users do not have a username, null should be passed to this method
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
        if (null !== $username) {
            throw new UsernameNotFoundException('Users do not have usernames');
        }

        // use the builder to return the new user
        $user = $this->userBuilder->build();

        return $user;
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        // because we're doing stateless auth, we should throw this exception
        throw new UnsupportedUserException();
    }

    /**
     * Whether this provider supports the given user class
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return 'Nerdery\Xbox\Bundle\UserBundle\Entity\User' === $class;
    }
}