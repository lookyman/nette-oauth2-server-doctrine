<?php

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var EntityRepository
	 */
	private $repository;

	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(AccessTokenEntity::class);
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
		$this->entityManager->persist($accessTokenEntity);
		$this->entityManager->flush();
	}

	/**
	 * @param string $tokenId
	 */
	public function revokeAccessToken($tokenId)
	{
		$this->repository->fetchOne((new AccessTokenQuery())->byIdentifier($tokenId))->setRevoked(true);
		$this->entityManager->flush();
	}

	/**
	 * @param string $tokenId
	 * @return bool
	 */
	public function isAccessTokenRevoked($tokenId)
	{
		return $this->repository->fetchOne((new AccessTokenQuery())->byIdentifier($tokenId))->isRevoked();
	}
}
