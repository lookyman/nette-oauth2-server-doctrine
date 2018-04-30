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

	public function byIdentifier(string $identifier): ScopeQuery
	{
		$this->filters[] = function (QueryBuilder $queryBuilder) use ($identifier): void {
			$queryBuilder->andWhere('s.identifier = :identifier')->setParameter('identifier', $identifier);
		};
		return $this;
	}

	protected function doCreateQuery(Queryable $repository): QueryBuilder
	{
		$queryBuilder = $repository->createQueryBuilder()->select('s')->from(ScopeEntity::class, 's');
		foreach ($this->filters as $filter) {
			$filter($queryBuilder);
		}
		return $queryBuilder;
	}

}
