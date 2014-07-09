<?php
/**
 * File GameController.php
 */

namespace Nerdery\Xbox\Bundle\WebBundle\Controller;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Nerdery\Xbox\Bundle\ApiBundle\Adapter\XboxAdapter;
use Nerdery\Xbox\Bundle\ApiBundle\Exception\XboxApiException;
use Nerdery\Xbox\Bundle\DataBundle\Date\DateParams;
use Nerdery\Xbox\Bundle\DataBundle\Entity\GamesCollection;
use Nerdery\Xbox\Bundle\DataBundle\String\Sanitizer;
use Nerdery\Xbox\Bundle\UserBundle\Entity\User;
use Nerdery\Xbox\Bundle\UserBundle\Security\Token\Authenticator\CookieAuthenticator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class GameController
 *
 * @author Nate Brunette <n@tebru.net>
 * @package Nerdery\Xbox\Bundle\WebBundle\Controller
 */
class GameController extends Controller
{
    /**
     * @DI\Inject("nerdery_xbox.xbox_adapter")
     *
     * @var XboxAdapter $apiAdapter
     */
    private $apiAdapter;

    /**
     * @DI\Inject("nerdery_xbox.date_params")
     *
     * @var DateParams $dateParams
     */
    private $dateParams;

    /**
     * @DI\Inject("logger")
     *
     * @var Logger $logger
     */
    private $logger;

    /**
     * @DI\Inject("nerdery_xbox.string_sanitizer")
     *
     * @var Sanitizer $sanitizer
     */
    private $sanitizer;

    /**
     * Games dashboard
     *
     * @Route("/", name="list")
     * @Method("GET")
     * @Template()
     *
     * @return Response
     */
    public function defaultAction()
    {
        try {
            $games = new GamesCollection($this->apiAdapter->getGames());
        } catch (XboxApiException $e)  {
            return $this->handleApiException($e);
        }

        // sort games by votes/title
        $games->sortWantedGames();
        $games->sortOwnedGames();

        return array(
            'ownedGames' => $games->getOwnedGames(),
            'wantedGames' => $games->getWantedGames(),
            'user' => $this->getUser(),
        );
    }

    /**
     * Add a game
     *
     * @Route("/add-game", name="add-game")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function addGameAction(Request $request)
    {
        try {
            $this->assertUser();
        } catch (AccessDeniedException $e) {
            return $this->goHome('Cannot access this resource at this time');
        }

        $title = $request->request->get('title');
        if (null === $title) {
            return $this->goHome('Request was made without title');
        }

        $title = $this->sanitizer->cleanup($title);
        if (empty($title)) {
            return $this->goHome('The title must not be empty');
        }

        try {
            $games = new GamesCollection($this->apiAdapter->getGames());
        } catch (XboxApiException $e)  {
            return $this->handleApiException($e);
        }

        if (true === $games->titleExists($title)) {
            return $this->goHome('This title already exists');
        }

        try {
            $this->apiAdapter->addGame($title);
        } catch (XboxApiException $e)  {
            return $this->handleApiException($e);
        }

        return $this->goHome(
            'Game added to wanted list',
            'success',
            new Cookie(CookieAuthenticator::KEY_COOKIE, $this->getUser()->getId(), $this->dateParams->getTomorrowAtMidnight())
        );
    }

    /**
     * Add a vote to a game
     *
     * @Route("/add-vote/{id}", name="add-vote")
     * @Method("GET")
     *
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function addVoteAction($id)
    {
        try {
            $this->assertUser();
        } catch (AccessDeniedException $e) {
            return $this->goHome('Cannot access this resource at this time');
        }

        try {
            $games = new GamesCollection($this->apiAdapter->getGames());
        } catch (XboxApiException $e)  {
            return $this->handleApiException($e);
        }

        if (false === $games->gameInWanted($id)) {
            return $this->goHome('This game may not be voted on');
        }

        try {
            $this->apiAdapter->addVote($id);
        } catch (XboxApiException $e)  {
            return $this->handleApiException($e);
        }

        return $this->goHome(
            'Vote added for game',
            'success',
            new Cookie(CookieAuthenticator::KEY_COOKIE, $this->getUser()->getId(), $this->dateParams->getTomorrowAtMidnight())
        );
    }

    /**
     * Mark game as owned
     *
     * @Route("/own-game/{id}", name="own-game")
     * @Method("GET")
     *
     * @param $id
     *
     * @return RedirectResponse
     */
    public function ownGameAction($id)
    {
        try {
            $this->apiAdapter->setGotGame($id);
        } catch (XboxApiException $e)  {
            return $this->handleApiException($e);
        }

        return $this->goHome('Game added to owned list.', 'success');
    }

    /**
     * Clear all games
     *
     * @Route("/clear-games", name="clear-games")
     * @Method("GET")
     *
     * @return RedirectResponse
     */
    public function clearGamesAction()
    {
        try {
            $this->apiAdapter->clearGames();
        } catch (XboxApiException $e)  {
            return $this->handleApiException($e);
        }

        return $this->goHome('All games cleared', 'success', new Cookie(CookieAuthenticator::KEY_COOKIE, null, 0));
    }

    /**
     * Asserts that a user can perform actions
     *
     * @throws AccessDeniedException
     */
    private function assertUser()
    {
        /** @var User $user */
        $user = $this->getUser();
        if (false === $user->getCanPerform()) {
            throw new AccessDeniedException();
        }
    }

    /**
     * Helper method to redirect to dashboard and set a cookie if necessary
     *
     * @param string|null $message
     * @param string $messageType
     * @param Cookie|null $cookie
     *
     * @return RedirectResponse
     */
    private function goHome($message = null, $messageType = 'error', Cookie $cookie = null)
    {
        if (null !== $message) {
            $this->get('session')->getFlashBag()->add($messageType, $message);
        }

        $response = new RedirectResponse($this->generateUrl('list'));

        if (null !== $cookie) {
            $response->headers->setCookie($cookie);

        }

        return $response;
    }

    /**
     * Handles an Api exception
     *
     * Currently logs the error and redirects to dashboard
     *
     * @todo add emailing and additional handling
     *
     * @param XboxApiException $e
     *
     * @return RedirectResponse
     */
    private function handleApiException(XboxApiException $e)
    {
        $this->logger->critical($e->getMessage());

        return $this->goHome(
            'An unexpected error occurred and your request could not be processed.  Please let us know if this error persists.'
        );
    }
}
