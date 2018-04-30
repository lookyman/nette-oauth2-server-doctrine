<?php
declare(strict_types=1);

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

	public function byIdentifier(string $identifier): AccessTokenQuery
	{
		$this->filters[] = function (QueryBuilder $queryBuilder) use ($identifier): void {
			$queryBuilder->andWhere('at.identifier = :identifier')->setParameter('identifier', $identifier);
		};
		return $this;
	}

	protected function doCreateQuery(Queryable $repository): QueryBuilder
	{
		$queryBuilder = $repository->createQueryBuilder()->select('at')->from(AccessTokenEntity::class, 'at');
		foreach ($this->filters as $filter) {
			$filter($queryBuilder);
		}
		return $queryBuilder;
	}

}
