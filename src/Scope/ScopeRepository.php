<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope;

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

	public function __construct(Registry $registry, ?callable $scopeFinalizer = null)
	{
		$this->registry = $registry;
		$this->scopeFinalizer = $scopeFinalizer ?: function (array $scopes): array {
			return $scopes;
		};
	}

	/**
	 * @param string $identifier
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getScopeEntityByIdentifier($identifier): ?ScopeEntity
	{
		/** @var ScopeEntity $entity */
		$entity = $this->registry->getManager()->getRepository(ScopeEntity::class)->fetchOne($this->createQuery()->byIdentifier($identifier));
		return $entity;
	}

	/**
	 * @param ScopeEntity[] $scopes
	 * @param string $grantType
	 * @param string|null $userIdentifier
	 * @return ScopeEntity[]
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function finalizeScopes(
		array $scopes,
		$grantType,
		ClientEntityInterface $clientEntity,
		$userIdentifier = null
	): array {
		return call_user_func($this->scopeFinalizer, $scopes, $grantType, $clientEntity, $userIdentifier);
	}

	protected function createQuery(): ScopeQuery
	{
		return new ScopeQuery();
	}

}
