<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock;

use Kdyby\Doctrine\Registry;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeRepository;

class AuthCodeRepositoryMock extends AuthCodeRepository
{

	/**
	 * @var AuthCodeQuery
	 */
	private $query;

	public function __construct(AuthCodeQuery $query, Registry $registry)
	{
		parent::__construct($registry);
		$this->query = $query;
	}

	protected function createQuery(): AuthCodeQuery
	{
		return $this->query;
	}

	public function createQueryOriginal(): AuthCodeQuery
	{
		return parent::createQuery();
	}

}
