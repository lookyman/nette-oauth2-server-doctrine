<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock;

use Kdyby\Doctrine\Registry;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientRepository;

class ClientRepositoryMock extends ClientRepository
{
	/**
	 * @var ClientQuery
	 */
	private $query;

	/**
	 * @param ClientQuery $query
	 * @param Registry $registry
	 * @param callable|null $secretValidator
	 */
	public function __construct(ClientQuery $query, Registry $registry, callable $secretValidator = null)
	{
		parent::__construct($registry, $secretValidator);
		$this->query = $query;
	}

	/**
	 * @return ClientQuery
	 */
	protected function createQuery(): ClientQuery
	{
		return $this->query;
	}

	/**
	 * @return ClientQuery
	 */
	public function createQueryOriginal(): ClientQuery
	{
		return parent::createQuery();
	}
}
