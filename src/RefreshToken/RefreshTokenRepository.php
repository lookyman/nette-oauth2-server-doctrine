<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
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
		$this->repository = $entityManager->getRepository(RefreshTokenEntity::class);
	}

	/**
	 * @return RefreshTokenEntity
	 */
	public function getNewRefreshToken()
	{
		return new RefreshTokenEntity();
	}

	/**
	 * @param RefreshTokenEntityInterface $refreshTokenEntity
	 */
	public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
	{
		$this->entityManager->persist($refreshTokenEntity);
		$this->entityManager->flush();
	}

	/**
	 * @param string $tokenId
	 */
	public function revokeRefreshToken($tokenId)
	{
		$this->repository->fetchOne((new RefreshTokenQuery())->byIdentifier($tokenId))->setRevoked(true);
		$this->entityManager->flush();
	}

	/**
	 * @param string $tokenId
	 * @return bool
	 */
	public function isRefreshTokenRevoked($tokenId)
	{
		return $this->repository->fetchOne((new RefreshTokenQuery())->byIdentifier($tokenId))->isRevoked();
	}
}
