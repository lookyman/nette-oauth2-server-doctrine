<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Client;

use Doctrine\ORM\QueryBuilder;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;

class ClientQuery extends QueryObject
{

	/**
	 * @var callable[]
	 */
	private $filters = [];

	public function byIdentifier(string $identifier): ClientQuery
	{
		$this->filters[] = function (QueryBuilder $queryBuilder) use ($identifier): void {
			$queryBuilder->andWhere('c.identifier = :identifier')->setParameter('identifier', $identifier);
		};
		return $this;
	}

	protected function doCreateQuery(Queryable $repository): QueryBuilder
	{
		$queryBuilder = $repository->createQueryBuilder()->select('c')->from(ClientEntity::class, 'c');
		foreach ($this->filters as $filter) {
			$filter($queryBuilder);
		}
		return $queryBuilder;
	}

}
