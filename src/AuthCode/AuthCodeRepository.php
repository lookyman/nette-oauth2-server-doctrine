<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode;

use Kdyby\Doctrine\QueryObject;
use Kdyby\Doctrine\Registry;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository implements AuthCodeRepositoryInterface
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
	 * @return AuthCodeEntity
	 */
	public function getNewAuthCode()
	{
		return new AuthCodeEntity();
	}

	/**
	 * @param AuthCodeEntityInterface $authCodeEntity
	 */
	public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
	{
		if ($authCodeEntity instanceof AuthCodeEntity) {
			$manager = $this->registry->getManager();
			$manager->persist($authCodeEntity);
			$manager->flush();
		}
	}

	/**
	 * @param string $codeId
	 */
	public function revokeAuthCode($codeId)
	{
		$manager = $this->registry->getManager();
		/** @var AuthCodeEntity|null $authCodeEntity */
		if ($authCodeEntity = $manager->getRepository(AuthCodeEntity::class)->fetchOne($this->createQuery()->byIdentifier($codeId))) {
			$authCodeEntity->setRevoked(true);
			$manager->flush();
		}
	}

	/**
	 * @param string $codeId
	 * @return bool
	 */
	public function isAuthCodeRevoked($codeId)
	{
		/** @var AuthCodeEntity|null $authCodeEntity */
		$authCodeEntity = $this->registry->getManager()->getRepository(AuthCodeEntity::class)->fetchOne($this->createQuery()->byIdentifier($codeId));
		return $authCodeEntity ? $authCodeEntity->isRevoked() : true;
	}

	/**
	 * @return QueryObject
	 */
	protected function createQuery(): QueryObject
	{
		return new AuthCodeQuery();
	}
}
