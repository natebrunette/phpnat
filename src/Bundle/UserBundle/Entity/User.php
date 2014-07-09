<?php
/**
 * File User.php
 */

namespace Nerdery\Xbox\Bundle\UserBundle\Entity;

use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 *
 * @author Nate Brunette <n@tebru.net>
 * @package Nerdery\Xbox\Bundle\UserBundle\Entity
 */
class User implements UserInterface
{
    /**
     * A unique user id
     *
     * This is not very important right now, but will allow for future improvements
     * by being able to uniquely identify users.
     *
     * @var int $id
     */
    private $id;

    /**
     * True if the user has already performed an action for the day
     *
     * @var bool $performed
     */
    private $performed;

    /**
     * True if the user can perform actions
     *
     * @var bool $canPerform
     */
    private $canPerform;

    /**
     * Constructor
     *
     * @param int $id
     * @param bool $performed
     * @param bool $canPerform
     */
    public function __construct($id, $performed, $canPerform)
    {
        $this->id = $id;
        $this->performed = (bool)$performed;
        $this->canPerform = (bool)$canPerform;
    }

    /**
     * Get the user id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get if the user has performed today
     *
     * @return bool
     */
    public function getHasPerformed()
    {
        return $this->performed;
    }

    /**
     * Set if the user has performed today
     *
     * @param bool $performed
     *
     * @return $this
     */
    public function setHasPerformed($performed)
    {
        $this->performed = (bool)$performed;

        return $this;
    }

    /**
     * Return if the user can perform action
     *
     * @return bool
     */
    public function getCanPerform()
    {
        return $this->canPerform;
    }

    /**
     * Set if a user can perform actions
     *
     * @param bool $performable
     *
     * @return $this
     */
    public function setCanPerform($performable)
    {
        $this->canPerform = $performable;

        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * We are defaulting to ROLE_USER because we never need to authenticate
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * Returns an empty string because the user does not have a password
     *
     * @return string The password
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * Returns null because the user does not have a password
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * Returns an empty string because the user does not have a username
     *
     * @return string The username
     */
    public function getUsername()
    {
        return '';
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     *
     * Sensitive information is never stored on this user object
     */
    public function eraseCredentials()
    {
        return null;
    }
}