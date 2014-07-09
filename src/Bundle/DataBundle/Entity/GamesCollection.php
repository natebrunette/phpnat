<?php
/**
 * File GamesCollection.php
 */

namespace Nerdery\Xbox\Bundle\DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class GamesCollection
 *
 * @author Nate Brunette <n@tebru.net>
 * @package Nerdery\Xbox\Bundle\DataBundle\Entity
 */
class GamesCollection extends ArrayCollection
{
    /**
     * Collection of wanted games
     *
     * @var GamesCollection $wantedGames
     */
    private $wantedGames;

    /**
     * Collection of owned games
     *
     * @var GamesCollection $ownedGames
     */
    private $ownedGames;


    /**
     * Get wanted games
     *
     * @return GamesCollection
     */
    public function getWantedGames()
    {
        if (null === $this->wantedGames || null === $this->ownedGames) {
            $this->splitGames();
        }

        return $this->wantedGames;
    }

    /**
     * Get owned games
     *
     * @return GamesCollection
     */
    public function getOwnedGames()
    {
        if (null === $this->wantedGames || null === $this->ownedGames) {
            $this->splitGames();
        }

        return $this->ownedGames;
    }

    /**
     * Sort wanted games by votes
     */
    public function sortWantedGames()
    {
        $games = $this->getWantedGames()->toArray();
        usort($games, function ($a, $b) { return $b->getVotes() - $a->getVotes(); });
        $this->wantedGames = new ArrayCollection($games);
    }

    /**
     * Sort owned games alphabetically
     */
    public function sortOwnedGames()
    {
        $games = $this->getOwnedGames()->toArray();
        usort($games, function ($a, $b) { return strcmp($a->getTitle(), $b->getTitle()); });
        $this->ownedGames = new ArrayCollection($games);
    }

    /**
     * Check if title exists in collection
     *
     * @param string $title
     *
     * @return bool
     */
    public function titleExists($title)
    {
        $exists = $this->exists(
            function ($key, $game) use ($title) {
                /** @var Game $game */
                return strtolower($game->getTitle()) === strtolower($title);
            }
        );

        return $exists;
    }

    /**
     * Check if game exists in wanted collection by id
     *
     * @param int $id
     *
     * @return bool
     */
    public function gameInWanted($id)
    {
        $exists = $this->getWantedGames()->exists(
            function ($key, $game) use ($id) {
                /** @var Game $game */
                return (int)$game->getId() === (int)$id;
            }
        );

        return $exists;
    }

    /**
     * Split collection into wanted and owned games
     */
    private function splitGames()
    {
        $ownedGames = clone $this;
        $wantedGames = $this->filter(
            function ($game) use ($ownedGames){
                /** @var Game $game */
                if ($game->getStatus() === Game::STATUS_WANTED) {
                    $ownedGames->removeElement($game);

                    return true;
                }

                return false;
            }
        );

        $this->ownedGames = $ownedGames;
        $this->wantedGames = $wantedGames;
    }
}