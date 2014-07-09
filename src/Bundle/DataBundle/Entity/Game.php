<?php
/**
 * File Game.php
 */

namespace Nerdery\Xbox\Bundle\DataBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Class Game
 *
 * A game entity that uses JMS serializer to hydrate
 *
 * @author Nate Brunette <n@tebru.net>
 * @package Nerdery\Xbox\Bundle\DataBundle\Entity
 */
class Game
{
    /**
     * State of a wanted game
     */
    const STATUS_WANTED = 'wantit';

    /**
     * State of an owned game
     */
    const STATUS_OWNED = 'gotit';

    /**
     * @JMS\Type("integer")
     * @var
     */
    private $id;

    /**
     * @JMS\Type("string")
     * @var
     */
    private $title;

    /**
     * @JMS\Type("integer")
     * @var
     */
    private $votes;
    /**
     * @JMS\Type("string")
     * @var
     */
    private $status;

    /**
     * @JMS\Type("string")
     * @var
     */
    private $ip;

    /**
     * @JMS\Type("string")
     * @var
     */
    private $votetime;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param mixed $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getVotetime()
    {
        return $this->votetime;
    }

    /**
     * @param mixed $votetime
     */
    public function setVotetime($votetime)
    {
        $this->votetime = $votetime;
    }
}