<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Client;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\QueryObject;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var EntityRepository
	 */
	private $repository;

	/**
	 * @var callable
	 */
	private $secretValidator;

	/**
	 * @param EntityManager $entityManager
	 * @param callable|null $secretValidator
	 */
	public function __construct(EntityManager $entityManager, callable $secretValidator = null)
	{
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(ClientEntity::class);
		$this->secretValidator = $secretValidator ?: function ($expected, $actual) { return hash_equals($expected, $actual); };
	}

	/**
	 * @param string $clientIdentifier
	 * @param string $grantType
	 * @param string|null $clientSecret
	 * @param bool $mustValidateSecret
	 * @return ClientEntityInterface|null
	 */
	public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
	{
		/** @var ClientEntity|null $client */
		$client = $this->repository->fetchOne($this->createQuery()->byIdentifier($clientIdentifier));
		return $client
			&& $client->getSecret()
			&& $mustValidateSecret
			&& !call_user_func($this->secretValidator, $client->getSecret(), $clientSecret)
			? null
			: $client;
	}

	/**
	 * @return QueryObject
	 */
	protected function createQuery(): QueryObject
	{
		return new ClientQuery();
	}
}
