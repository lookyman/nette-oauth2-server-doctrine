<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock;

use League\Event\EmitterInterface;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\GrantTypeInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;

class CustomGrantMock implements GrantTypeInterface
{
	/**
	 * @param EmitterInterface $emitter
	 * @return $this
	 */
	public function setEmitter(EmitterInterface $emitter = null)
	{
		return $this;
	}

	/**
	 * @return EmitterInterface
	 */
	public function getEmitter()
	{
	}

	/**
	 * @param \DateInterval $refreshTokenTTL
	 */
	public function setRefreshTokenTTL(\DateInterval $refreshTokenTTL)
	{
	}

	/**
	 * @return string
	 */
	public function getIdentifier()
	{
		return 'custom';
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseTypeInterface $responseType
	 * @param \DateInterval $accessTokenTTL
	 * @return ResponseTypeInterface
	 */
	public function respondToAccessTokenRequest(
		ServerRequestInterface $request,
		ResponseTypeInterface $responseType,
		\DateInterval $accessTokenTTL
	)
	{
	}

	/**
	 * @param ServerRequestInterface $request
	 * @return bool
	 */
	public function canRespondToAuthorizationRequest(ServerRequestInterface $request)
	{
		return false;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @return AuthorizationRequest
	 */
	public function validateAuthorizationRequest(ServerRequestInterface $request)
	{
	}

	/**
	 * @param AuthorizationRequest $authorizationRequest
	 * @return ResponseTypeInterface
	 */
	public function completeAuthorizationRequest(AuthorizationRequest $authorizationRequest)
	{
	}

	/**
	 * @param ServerRequestInterface $request
	 * @return bool
	 */
	public function canRespondToAccessTokenRequest(ServerRequestInterface $request)
	{
		return false;
	}

	/**
	 * @param ClientRepositoryInterface $clientRepository
	 */
	public function setClientRepository(ClientRepositoryInterface $clientRepository)
	{
	}

	/**
	 * @param AccessTokenRepositoryInterface $accessTokenRepository
	 */
	public function setAccessTokenRepository(AccessTokenRepositoryInterface $accessTokenRepository)
	{
	}

	/**
	 * @param ScopeRepositoryInterface $scopeRepository
	 */
	public function setScopeRepository(ScopeRepositoryInterface $scopeRepository)
	{
	}

	/**
	 * @param CryptKey $privateKey
	 */
	public function setPrivateKey(CryptKey $privateKey)
	{
	}

	/**
	 * @param CryptKey $publicKey
	 */
	public function setPublicKey(CryptKey $publicKey)
	{
	}
}
