<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope;

use Kdyby\Doctrine\InvalidStateException;
use Kdyby\Doctrine\QueryException;
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
	 * @var callable
	 */
	private $scopeFinalizer;

	/**
	 * @param Registry $registry
	 * @param callable|null $scopeFinalizer
	 */
	public function __construct(Registry $registry, callable $scopeFinalizer = null)
	{
		$this->registry = $registry;
		$this->scopeFinalizer = $scopeFinalizer ?: function (array $scopes) { return $scopes; };
	}

	/**
	 * @param string $identifier
	 * @return ScopeEntity|null
	 * @throws InvalidStateException
	 * @throws QueryException
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
		return call_user_func($this->scopeFinalizer, $scopes, $grantType, $clientEntity, $userIdentifier);
	}

	/**
	 * @return ScopeQuery
	 */
	protected function createQuery(): ScopeQuery
	{
		return new ScopeQuery();
	}
}
