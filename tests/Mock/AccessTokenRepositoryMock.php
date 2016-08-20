<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\QueryObject;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenRepository;

class AccessTokenRepositoryMock extends AccessTokenRepository
{
	/**
	 * @var QueryObject
	 */
	private $query;

	/**
	 * @param QueryObject $query
	 * @param EntityManager $entityManager
	 */
	public function __construct(QueryObject $query, EntityManager $entityManager)
	{
		parent::__construct($entityManager);
		$this->query = $query;
	}

	/**
	 * @return QueryObject
	 */
	protected function createQuery(): QueryObject
	{
		return $this->query;
	}

	/**
	 * @return QueryObject
	 */
	public function createQueryOriginal(): QueryObject
	{
		return parent::createQuery();
	}
}
