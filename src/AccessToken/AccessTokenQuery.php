<?php

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken;

use Doctrine\ORM\QueryBuilder;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;

class AccessTokenQuery extends QueryObject
{
	/**
	 * @var callable[]
	 */
	private $filters = [];

	/**
	 * @param string $identifier
	 * @return self
	 */
	public function byIdentifier($identifier)
	{
		$this->filters[] = function (QueryBuilder $queryBuilder) use ($identifier) {
			$queryBuilder->andWhere('at.identifier = :identifier')->setParameter('identifier', $identifier);
		};
		return $this;
	}

	/**
	 * @param Queryable $repository
	 * @return QueryBuilder
	 */
	protected function doCreateQuery(Queryable $repository)
	{
		$queryBuilder = $repository->createQueryBuilder()->select('at')->from(AccessTokenEntity::class, 'at');
		foreach ($this->filters as $filter) {
			$filter($queryBuilder);
		}
		return $queryBuilder;
	}
}
