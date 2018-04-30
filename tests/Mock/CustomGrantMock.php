<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock;

use Defuse\Crypto\Key;
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

	public function setEmitter(?EmitterInterface $emitter = null): self
	{
		return $this;
	}

	public function getEmitter(): EmitterInterface
	{
	}

	public function setRefreshTokenTTL(\DateInterval $refreshTokenTTL): void
	{
	}

	public function getIdentifier(): string
	{
		return 'custom';
	}

	public function respondToAccessTokenRequest(
		ServerRequestInterface $request,
		ResponseTypeInterface $responseType,
		\DateInterval $accessTokenTTL
	): ResponseTypeInterface {
	}

	public function canRespondToAuthorizationRequest(ServerRequestInterface $request): bool
	{
		return false;
	}

	public function validateAuthorizationRequest(ServerRequestInterface $request): AuthorizationRequest
	{
	}

	public function completeAuthorizationRequest(AuthorizationRequest $authorizationRequest): ResponseTypeInterface
	{
	}

	public function canRespondToAccessTokenRequest(ServerRequestInterface $request): bool
	{
		return false;
	}

	public function setClientRepository(ClientRepositoryInterface $clientRepository): void
	{
	}

	public function setAccessTokenRepository(AccessTokenRepositoryInterface $accessTokenRepository): void
	{
	}

	public function setScopeRepository(ScopeRepositoryInterface $scopeRepository): void
	{
	}

	/**
	 * @param string $scope
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function setDefaultScope($scope): void
	{
	}

	public function setPrivateKey(CryptKey $privateKey): void
	{
	}

	/**
	 * @param string|Key|null $key
	 */
	public function setEncryptionKey($key = null): void
	{
	}

}
