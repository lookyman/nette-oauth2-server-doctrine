<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock;

use Kdyby\Doctrine\QueryObject;
use Kdyby\Doctrine\Registry;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenRepository;

class AccessTokenRepositoryMock extends AccessTokenRepository
{
	/**
	 * @var QueryObject
	 */
	private $query;

	/**
	 * @param QueryObject $query
	 * @param Registry $registry
	 */
	public function __construct(QueryObject $query, Registry $registry)
	{
		parent::__construct($registry);
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
