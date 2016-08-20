<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\QueryObject;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientRepository;

class ClientRepositoryMock extends ClientRepository
{
	/**
	 * @var QueryObject
	 */
	private $query;

	/**
	 * @param QueryObject $query
	 * @param EntityManager $entityManager
	 * @param callable|null $secretValidator
	 */
	public function __construct(QueryObject $query, EntityManager $entityManager, callable $secretValidator = null)
	{
		parent::__construct($entityManager, $secretValidator);
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
