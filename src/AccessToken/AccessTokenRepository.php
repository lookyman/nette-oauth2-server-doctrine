<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken;

use Kdyby\Doctrine\QueryObject;
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

	/**
	 * @param Registry $registry
	 */
	public function __construct(Registry $registry)
	{
		$this->registry = $registry;
	}

	/**
	 * @param ClientEntityInterface $clientEntity
	 * @param ScopeEntityInterface[] $scopes
	 * @param string|null $userIdentifier
	 * @return AccessTokenEntity
	 */
	public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
	{
		$accessToken = new AccessTokenEntity();
		$accessToken->setClient($clientEntity);
		foreach ($scopes as $scope) {
			$accessToken->addScope($scope);
		}
		$accessToken->setUserIdentifier($userIdentifier);
		return $accessToken;
	}

	/**
	 * @param AccessTokenEntityInterface $accessTokenEntity
	 */
	public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
	{
		if ($accessTokenEntity instanceof AccessTokenEntity) {
			$manager = $this->registry->getManager();
			$manager->persist($accessTokenEntity);
			$manager->flush();
		}
	}

	/**
	 * @param string $tokenId
	 */
	public function revokeAccessToken($tokenId)
	{
		$manager = $this->registry->getManager();
		/** @var AccessTokenEntity|null $accessTokenEntity */
		if ($accessTokenEntity = $manager->getRepository(AccessTokenEntity::class)->fetchOne($this->createQuery()->byIdentifier($tokenId))) {
			$accessTokenEntity->setRevoked(true);
			$manager->flush();
		}
	}

	/**
	 * @param string $tokenId
	 * @return bool
	 */
	public function isAccessTokenRevoked($tokenId)
	{
		/** @var AccessTokenEntity|null $accessTokenEntity */
		$accessTokenEntity = $this->registry->getManager()->getRepository(AccessTokenEntity::class)->fetchOne($this->createQuery()->byIdentifier($tokenId));
		return $accessTokenEntity ? $accessTokenEntity->isRevoked() : true;
	}

	/**
	 * @return QueryObject
	 */
	protected function createQuery(): QueryObject
	{
		return new AccessTokenQuery();
	}
}
