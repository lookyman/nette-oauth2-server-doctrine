<?php
declare(strict_types=1);

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

	public function byIdentifier(string $identifier): AuthCodeQuery
	{
		$this->filters[] = function (QueryBuilder $queryBuilder) use ($identifier): void {
			$queryBuilder->andWhere('ac.identifier = :identifier')->setParameter('identifier', $identifier);
		};
		return $this;
	}

	protected function doCreateQuery(Queryable $repository): QueryBuilder
	{
		$queryBuilder = $repository->createQueryBuilder()->select('ac')->from(AuthCodeEntity::class, 'ac');
		foreach ($this->filters as $filter) {
			$filter($queryBuilder);
		}
		return $queryBuilder;
	}

}
