<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope;

use Doctrine\ORM\QueryBuilder;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;

class ScopeQuery extends QueryObject
{
	/**
	 * @var callable[]
	 */
	private $filters = [];

	/**
	 * @param string $identifier
	 * @return self
	 */
	public function byIdentifier(string $identifier): ScopeQuery
	{
		$this->filters[] = function (QueryBuilder $queryBuilder) use ($identifier) {
			$queryBuilder->andWhere('s.identifier = :identifier')->setParameter('identifier', $identifier);
		};
		return $this;
	}

	/**
	 * @param Queryable $repository
	 * @return QueryBuilder
	 */
	protected function doCreateQuery(Queryable $repository)
	{
		$queryBuilder = $repository->createQueryBuilder()->select('s')->from(ScopeEntity::class, 's');
		foreach ($this->filters as $filter) {
			$filter($queryBuilder);
		}
		return $queryBuilder;
	}
}
