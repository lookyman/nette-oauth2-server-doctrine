<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken;

use Kdyby\Doctrine\Registry;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{

	/**
	 * @var Registry
	 */
	private $registry;

	public function __construct(Registry $registry)
	{
		$this->registry = $registry;
	}

	public function getNewRefreshToken(): RefreshTokenEntity
	{
		return new RefreshTokenEntity();
	}

	public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
	{
		if ($refreshTokenEntity instanceof RefreshTokenEntity) {
			$manager = $this->registry->getManager();
			$manager->persist($refreshTokenEntity);
			$manager->flush();
		}
	}

	/**
	 * @param string $tokenId
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function revokeRefreshToken($tokenId): void
	{
		$manager = $this->registry->getManager();
		/** @var RefreshTokenEntity|null $refreshTokenEntity */
		$refreshTokenEntity = $manager->getRepository(RefreshTokenEntity::class)->fetchOne($this->createQuery()->byIdentifier($tokenId));
		if ($refreshTokenEntity !== null) {
			$refreshTokenEntity->setRevoked(true);
			$manager->flush();
		}
	}

	/**
	 * @param string $tokenId
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function isRefreshTokenRevoked($tokenId): bool
	{
		/** @var RefreshTokenEntity|null $refreshTokenEntity */
		$refreshTokenEntity = $this->registry->getManager()->getRepository(RefreshTokenEntity::class)->fetchOne($this->createQuery()->byIdentifier($tokenId));
		return $refreshTokenEntity !== null ? $refreshTokenEntity->isRevoked() : true;
	}

	protected function createQuery(): RefreshTokenQuery
	{
		return new RefreshTokenQuery();
	}

}
