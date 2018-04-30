<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken;

use Kdyby\Doctrine\Registry;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{

	/**
	 * @var Registry
	 */
	private $registry;

	public function __construct(Registry $registry)
	{
		$this->registry = $registry;
	}

	/**
	 * @param ScopeEntityInterface[] $scopes
	 * @param string|null $userIdentifier
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntity
	{
		$accessToken = new AccessTokenEntity();
		$accessToken->setClient($clientEntity);
		foreach ($scopes as $scope) {
			$accessToken->addScope($scope);
		}
		$accessToken->setUserIdentifier($userIdentifier);
		return $accessToken;
	}

	public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
	{
		if ($accessTokenEntity instanceof AccessTokenEntity) {
			$manager = $this->registry->getManager();
			$manager->persist($accessTokenEntity);
			$manager->flush();
		}
	}

	/**
	 * @param string $tokenId
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function revokeAccessToken($tokenId): void
	{
		$manager = $this->registry->getManager();
		/** @var AccessTokenEntity|null $accessTokenEntity */
		$accessTokenEntity = $manager->getRepository(AccessTokenEntity::class)->fetchOne($this->createQuery()->byIdentifier($tokenId));
		if ($accessTokenEntity !== null) {
			$accessTokenEntity->setRevoked(true);
			$manager->flush();
		}
	}

	/**
	 * @param string $tokenId
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function isAccessTokenRevoked($tokenId): bool
	{
		/** @var AccessTokenEntity|null $accessTokenEntity */
		$accessTokenEntity = $this->registry->getManager()->getRepository(AccessTokenEntity::class)->fetchOne($this->createQuery()->byIdentifier($tokenId));
		return $accessTokenEntity !== null ? $accessTokenEntity->isRevoked() : true;
	}

	protected function createQuery(): AccessTokenQuery
	{
		return new AccessTokenQuery();
	}

}
