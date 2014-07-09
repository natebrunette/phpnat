<?php
/**
 * File XboxAdapter.php
 */

namespace Nerdery\Xbox\Bundle\ApiBundle\Adapter;

use JMS\Serializer\Serializer;
use Nerdery\Xbox\Bundle\ApiBundle\Exception\XboxApiException;
use Nerdery\Xbox\Bundle\DataBundle\String\Sanitizer;

/**
 * Class XboxAdapter
 *
 * Adapts the Nerdery xbox api
 *
 * @author Nate Brunette <n@tebru.net>
 * @package Nerdery\Xbox\Bundle\ApiBundle\Adapter
 */
class XboxAdapter
{
    /**#@+
     * Api method names
     */
    const METHOD_CHECK_KEY = 'checkKey';
    const METHOD_GET_GAMES = 'getGames';
    const METHOD_ADD_VOTE = 'addVote';
    const METHOD_ADD_GAME = 'addGame';
    const METHOD_SET_GOT_GAME = 'setGotIt';
    const METHOD_CLEAR_GAMES = 'clearGames';
    /**#@-*/

    /**
     * A soap client for connecting to the api
     *
     * @var \SoapClient $client
     */
    private $client;

    /**
     * Api key for connecting to the api
     *
     * @var string $apiKey
     */
    private $apiKey;

    /**
     * String sanitizer
     *
     * @var Sanitizer $sanitizer
     */
    private $sanitizer;

    /**
     * JMS serializer
     *
     * @var Serializer $serializer
     */
    private $serializer;

    /**
     * Constructor
     *
     * Checks for validity of api here so we can ensure connectivity and do not have to check
     * multiple times.
     *
     * @param \SoapClient $client
     * @param string $apiKey
     * @param Sanitizer $sanitizer
     * @param Serializer $serializer
     *
     * @throws XboxApiException if the key is not valid
     */
    public function __construct(\SoapClient $client, $apiKey, Sanitizer $sanitizer, Serializer $serializer)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->sanitizer = $sanitizer;
        $this->serializer = $serializer;

        $response = $this->checkKey();
        if (false === $response) {
            throw new XboxApiException(XboxApiException::ERROR_API_KEY_INVALID, XboxApiException::ERROR_CODE_API);
        }
    }

    /**
     * Checks validity of api key
     *
     * @return bool
     *     TRUE on success, FALSE on failure
     */
    public function checkKey()
    {
        $params = $this->getParams();
        $response = $this->makeRequest(self::METHOD_CHECK_KEY, $params);

        return $response;
    }

    /**
     * Get an array of games
     *
     * Each game comes back as a StdClass and will get converted to a Game entity
     *
     * @todo if performance becomes an issue, consider looking at this method as I did
     * not check how performant the conversion is.
     *
     * @return array|bool
     *     An array of games on success, FALSE on failure
     */
    public function getGames()
    {
        $params = $this->getParams();
        $response = $this->makeRequest(self::METHOD_GET_GAMES, $params);

        $this->assertResponse($response);

        // convert StdClass to Game entity by json encoding the object and using
        // jms deserializer to apply it to the Game entity
        foreach ($response as $key => $game) {
            $game->title = $this->sanitizer->reverseSanitizeForDatabase($game->title);
            $response[$key] = $this->serializer->deserialize(
                json_encode($game),
                'Nerdery\Xbox\Bundle\DataBundle\Entity\Game',
                'json'
            );
        }

        return $response;
    }

    /**
     * Add a vote to a game
     *
     * @param int $id
     * @return bool
     *     TRUE on success, FALSE on failure
     */
    public function addVote($id)
    {
        $id = (int)$id;
        $params = $this->getParams(array('id' => $id));
        $response = $this->makeRequest(self::METHOD_ADD_VOTE, $params);

        $this->assertResponse($response);

        return $response;
    }

    /**
     * Add a game
     *
     * @param string $title The game title
     *
     * @return bool
     *     TRUE on success, FALSE on failure
     */
    public function addGame($title)
    {
        $title = $this->sanitizer->sanitizeForDatabase($title);
        $params = $this->getParams(array('title' => $title));
        $response = $this->makeRequest(self::METHOD_ADD_GAME, $params);

        $this->assertResponse($response);

        return $response;
    }

    /**
     * Sets a game to owned status
     *
     * @param int $id
     *
     * @return bool
     *     TRUE on success, FALSE on failure
     */
    public function setGotGame($id)
    {
        $id = (int)$id;
        $params = $this->getParams(array('id' => $id));
        $response = $this->makeRequest(self::METHOD_SET_GOT_GAME, $params);

        $this->assertResponse($response);

        return $response;
    }

    /**
     * Clear all games
     *
     * @return bool
     *     TRUE on success, FALSE on failure
     */
    public function clearGames()
    {
        $params = $this->getParams();
        $response = $this->makeRequest(self::METHOD_CLEAR_GAMES, $params);

        $this->assertResponse($response);

        return $response;
    }

    /**
     * Helper method to create an api request
     *
     * @param string $method Api method
     * @param array $params Array of parameters to send with request
     *
     * @return mixed
     * @throws XboxApiException if soap service cannot be connect to
     */
    private function makeRequest($method, array $params)
    {
        try {
            $response = $this->client->__soapCall($method, $params);
        } catch (\SoapFault $e) {
            $message = $e->getMessage() . ' with error code ' . $e->getCode();
            throw new XboxApiException($message, XboxApiException::ERROR_CODE_SOAP);
        }

        return $response;
    }

    /**
     * Asserts that the response is not FALSE
     *
     * FALSE is the only indication of failure from the api
     *
     * @param $response
     *
     * @throws XboxApiException if request failed
     */
    private function assertResponse($response)
    {
        if (false === $response) {
            throw new XboxApiException(XboxApiException::ERROR_ID_INVALID, XboxApiException::ERROR_CODE_API);
        }
    }

    /**
     * Get an array of parameters to send to the api
     *
     * The api key will always be sent with the request.  Optionally, any array
     * passed in get added to the array of parameters.
     *
     * @param array $additionalParams Any additional parameters for request
     *
     * @return array
     */
    private function getParams(array $additionalParams = array())
    {
        $params = array('apiKey' => $this->apiKey);
        $params = array_merge($params, $additionalParams);

        return $params;
    }
}