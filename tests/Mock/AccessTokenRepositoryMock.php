<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock;

use Kdyby\Doctrine\Registry;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenRepository;

class AccessTokenRepositoryMock extends AccessTokenRepository
{
	/**
	 * @var AccessTokenQuery
	 */
	private $query;

	/**
	 * @param AccessTokenQuery $query
	 * @param Registry $registry
	 */
	public function __construct(AccessTokenQuery $query, Registry $registry)
	{
		parent::__construct($registry);
		$this->query = $query;
	}

	/**
	 * @return AccessTokenQuery
	 */
	protected function createQuery(): AccessTokenQuery
	{
		return $this->query;
	}

	/**
	 * @return AccessTokenQuery
	 */
	public function createQueryOriginal(): AccessTokenQuery
	{
		return parent::createQuery();
	}
}
