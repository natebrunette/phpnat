<?php
/**
 * File CookieAuthenticator.php
 */

namespace Nerdery\Xbox\Bundle\UserBundle\Security\Token\Authenticator;

use Nerdery\Xbox\Bundle\DataBundle\Date\DateParams;
use Nerdery\Xbox\Bundle\UserBundle\Entity\User;
use Nerdery\Xbox\Bundle\UserBundle\Security\Provider\UserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class CookieAuthenticator
 *
 * Authenticates a token based on a cookie.
 *
 * @author Nate Brunette <n@tebru.net>
 * @package Nerdery\Xbox\Bundle\UserBundle\Security\Token\Authenticator
 */
class CookieAuthenticator implements SimplePreAuthenticatorInterface
{
    /**
     * The cookie key name used to store the user id
     *
     * @todo consider a different class to store this value in
     */
    const KEY_COOKIE = 'nerdery_xbox_user';

    /**
     * An object that performs date calculations
     *
     * @var DateParams $dateParams
     */
    private $dateParams;

    /**
     * Constructor
     *
     * @param DateParams $dateParams
     */
    public function __construct(DateParams $dateParams)
    {
        $this->dateParams = $dateParams;
    }

    /**
     * Creates a token
     *
     * @param Request $request
     * @param string $providerKey
     *
     * @return PreAuthenticatedToken
     */
    public function createToken(Request $request, $providerKey)
    {
        $uniqueId = $request->cookies->get(self::KEY_COOKIE);

        $token = new PreAuthenticatedToken('anon.', $uniqueId, $providerKey);

        return $token;
    }

    /**
     * Authenticates the user token
     *
     * Since there isn't any authentication that needs to happen, just make sure the
     * user object is created successfully and pass along a new token.
     *
     * @param TokenInterface $token
     * @param UserProviderInterface|UserProvider $userProvider
     * @param string $providerKey
     * @return PreAuthenticatedToken
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $userId = $token->getCredentials();
        $userBuilder = $userProvider->getUserBuilder();
        $userBuilder->setId($userId);

        if (null !== $userId) {
            $userBuilder->setHasPerformed(true);
            $userBuilder->setCanPerform(false);
        }

        if (true === $this->dateParams->isWeekend()) {
            $userBuilder->setCanPerform(false);
        }

        /** @var User $user */
        $user = $userProvider->loadUserByUsername(null);
        $token = new PreAuthenticatedToken($user, $user->getId(), $providerKey, $user->getRoles());

        return $token;
    }

    /**
     * Determines if $token is a token this authenticator supports
     *
     * @param TokenInterface $token
     * @param string $providerKey
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken;
    }
}