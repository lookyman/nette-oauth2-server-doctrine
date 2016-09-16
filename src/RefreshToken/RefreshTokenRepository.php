<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Kdyby\Doctrine\InvalidStateException;
use Kdyby\Doctrine\QueryException;
use Kdyby\Doctrine\Registry;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
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
	 * @return RefreshTokenEntity
	 */
	public function getNewRefreshToken()
	{
		return new RefreshTokenEntity();
	}

	/**
	 * @param RefreshTokenEntityInterface $refreshTokenEntity
	 * @throws ORMInvalidArgumentException
	 * @throws OptimisticLockException
	 */
	public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
	{
		if ($refreshTokenEntity instanceof RefreshTokenEntity) {
			$manager = $this->registry->getManager();
			$manager->persist($refreshTokenEntity);
			$manager->flush();
		}
	}

	/**
	 * @param string $tokenId
	 * @throws InvalidStateException
	 * @throws QueryException
	 * @throws OptimisticLockException
	 */
	public function revokeRefreshToken($tokenId)
	{
		$manager = $this->registry->getManager();
		/** @var RefreshTokenEntity|null $refreshTokenEntity */
		if ($refreshTokenEntity = $manager->getRepository(RefreshTokenEntity::class)->fetchOne($this->createQuery()->byIdentifier($tokenId))) {
			$refreshTokenEntity->setRevoked(true);
			$manager->flush();
		}
	}

	/**
	 * @param string $tokenId
	 * @return bool
	 * @throws InvalidStateException
	 * @throws QueryException
	 */
	public function isRefreshTokenRevoked($tokenId)
	{
		/** @var RefreshTokenEntity $refreshTokenEntity */
		$refreshTokenEntity = $this->registry->getManager()->getRepository(RefreshTokenEntity::class)->fetchOne($this->createQuery()->byIdentifier($tokenId));
		return $refreshTokenEntity ? $refreshTokenEntity->isRevoked() : true;
	}

	/**
	 * @return RefreshTokenQuery
	 */
	protected function createQuery(): RefreshTokenQuery
	{
		return new RefreshTokenQuery();
	}
}
