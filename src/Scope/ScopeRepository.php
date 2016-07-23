<?php

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository implements ScopeRepositoryInterface
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var EntityRepository
	 */
	private $repository;

	/**
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(ScopeEntity::class);
	}

	/**
	 * @param string $identifier
	 * @return ScopeEntityInterface
	 */
	public function getScopeEntityByIdentifier($identifier)
	{
		return $this->repository->fetchOne((new ScopeQuery())->byIdentifier($identifier));
	}

	/**
	 * @param array $scopes
	 * @param string $grantType
	 * @param ClientEntityInterface $clientEntity
	 * @param string|null $userIdentifier
	 * @return ScopeEntityInterface[]
	 */
	public function finalizeScopes(
		array $scopes,
		$grantType,
		ClientEntityInterface $clientEntity,
		$userIdentifier = null
	)
	{
		return $scopes;
	}
}
