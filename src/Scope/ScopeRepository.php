<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope;

use Kdyby\Doctrine\QueryObject;
use Kdyby\Doctrine\Registry;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository implements ScopeRepositoryInterface
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
	 * @param string $identifier
	 * @return ScopeEntity|null
	 */
	public function getScopeEntityByIdentifier($identifier)
	{
		return $this->registry->getManager()->getRepository(ScopeEntity::class)->fetchOne($this->createQuery()->byIdentifier($identifier));
	}

	/**
	 * @param ScopeEntity[] $scopes
	 * @param string $grantType
	 * @param ClientEntityInterface $clientEntity
	 * @param string|null $userIdentifier
	 * @return ScopeEntity[]
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

	/**
	 * @return QueryObject
	 */
	protected function createQuery(): QueryObject
	{
		return new ScopeQuery();
	}
}
