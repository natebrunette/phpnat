<?php
/**
 * File UserBuilder.php
 */

namespace Nerdery\Xbox\Bundle\UserBundle\Builder;

use Nerdery\Xbox\Bundle\UserBundle\Entity\User;

/**
 * Class UserBuilder
 *
 * Creates a user object
 *
 * @author Nate Brunette <n@tebru.net>
 * @package Nerdery\Xbox\Bundle\UserBundle\Builder
 */
class UserBuilder
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
     * @var bool
     */
    private $performed = false;

    /**
     * True if a user can perform actions
     *
     * @var bool
     */
    private $canPerform = true;

    /**
     * Set the user id
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set if the user has performed an action today or not
     *
     * @param bool $performed
     */
    public function setHasPerformed($performed)
    {
        $this->performed = (bool)$performed;
    }

    /**
     * Set if a user can perform actions
     *
     * @param bool $performable
     */
    public function setCanPerform($performable)
    {
        $this->canPerform = (bool)$performable;
    }

    /**
     * Build the user object
     *
     * If id is not set, create a unique one
     *
     * @return User
     */
    public function build()
    {
        if (null === $this->id) {
            $this->id = uniqid();
        }

        $user = new User($this->id, $this->performed, $this->canPerform);

        return $user;
    }
}