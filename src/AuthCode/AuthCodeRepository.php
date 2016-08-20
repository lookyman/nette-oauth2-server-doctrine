<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\QueryObject;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository implements AuthCodeRepositoryInterface
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
		$this->repository = $entityManager->getRepository(AuthCodeEntity::class);
	}

	/**
	 * @return AuthCodeEntityInterface
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
		$this->entityManager->persist($authCodeEntity);
		$this->entityManager->flush();
	}

	/**
	 * @param string $codeId
	 */
	public function revokeAuthCode($codeId)
	{
		$this->repository->fetchOne($this->createQuery()->byIdentifier($codeId))->setRevoked(true);
		$this->entityManager->flush();
	}

	/**
	 * @param string $codeId
	 * @return bool
	 */
	public function isAuthCodeRevoked($codeId)
	{
		return $this->repository->fetchOne($this->createQuery()->byIdentifier($codeId))->isRevoked();
	}

	/**
	 * @return QueryObject
	 */
	protected function createQuery(): QueryObject
	{
		return new AuthCodeQuery();
	}
}
