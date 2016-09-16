<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock;

use Kdyby\Doctrine\Registry;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenRepository;

class RefreshTokenRepositoryMock extends RefreshTokenRepository
{
	/**
	 * @var RefreshTokenQuery
	 */
	private $query;

	/**
	 * @param RefreshTokenQuery $query
	 * @param Registry $registry
	 */
	public function __construct(RefreshTokenQuery $query, Registry $registry)
	{
		parent::__construct($registry);
		$this->query = $query;
	}

	/**
	 * @return RefreshTokenQuery
	 */
	protected function createQuery(): RefreshTokenQuery
	{
		return $this->query;
	}

	/**
	 * @return RefreshTokenQuery
	 */
	public function createQueryOriginal(): RefreshTokenQuery
	{
		return parent::createQuery();
	}
}
