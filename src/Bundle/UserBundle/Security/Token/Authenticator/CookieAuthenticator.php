<?php
/**
 * File CookieAuthenticator.php
 */

namespace Nerdery\Xbox\Bundle\UserBundle\Security\Token\Authenticator;

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
     * Creates a token
     *
     * @param Request $request
     * @param $providerKey
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
     * @param $providerKey
     * @return PreAuthenticatedToken
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $userId = $token->getCredentials();
        $userBuilder = $userProvider->getUserBuilder();
        $userBuilder->setId($userId);

        if (null !== $userId) {
            $userBuilder->setHasPerformed(true);
        }

        $user = $userProvider->loadUserByUsername(null);
        $token = new PreAuthenticatedToken($user, $token->getCredentials(), $providerKey, $user->getRoles());

        return $token;
    }

    /**
     * Determines if $token is a token this authenticator supports
     *
     * @param TokenInterface $token
     * @param $providerKey
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken;
    }
}