<?php

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode;

use Doctrine\ORM\QueryBuilder;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;

class AuthCodeQuery extends QueryObject
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
			$queryBuilder->andWhere('ac.identifier = :identifier')->setParameter('identifier', $identifier);
		};
		return $this;
	}

	/**
	 * @param Queryable $repository
	 * @return QueryBuilder
	 */
	protected function doCreateQuery(Queryable $repository)
	{
		$queryBuilder = $repository->createQueryBuilder()->select('ac')->from(AuthCodeEntity::class, 'ac');
		foreach ($this->filters as $filter) {
			$filter($queryBuilder);
		}
		return $queryBuilder;
	}
}
