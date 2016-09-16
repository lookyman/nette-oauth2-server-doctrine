<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock;

use Kdyby\Doctrine\Registry;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeRepository;

class ScopeRepositoryMock extends ScopeRepository
{
	/**
	 * @var ScopeQuery
	 */
	private $query;

	/**
	 * @param ScopeQuery $query
	 * @param Registry $registry
	 */
	public function __construct(ScopeQuery $query, Registry $registry)
	{
		parent::__construct($registry);
		$this->query = $query;
	}

	/**
	 * @return ScopeQuery
	 */
	protected function createQuery(): ScopeQuery
	{
		return $this->query;
	}

	/**
	 * @return ScopeQuery
	 */
	public function createQueryOriginal(): ScopeQuery
	{
		return parent::createQuery();
	}
}
