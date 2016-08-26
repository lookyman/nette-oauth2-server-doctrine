<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken;

use Kdyby\Doctrine\QueryObject;
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
	 */
	public function isRefreshTokenRevoked($tokenId)
	{
		/** @var RefreshTokenEntity $refreshTokenEntity */
		$refreshTokenEntity = $this->registry->getManager()->getRepository(RefreshTokenEntity::class)->fetchOne($this->createQuery()->byIdentifier($tokenId));
		return $refreshTokenEntity ? $refreshTokenEntity->isRevoked() : true;
	}

	/**
	 * @return QueryObject
	 */
	protected function createQuery(): QueryObject
	{
		return new RefreshTokenQuery();
	}
}
