<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Client;

use Kdyby\Doctrine\QueryObject;
use Kdyby\Doctrine\Registry;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
	/**
	 * @var Registry
	 */
	private $registry;

	/**
	 * @var callable
	 */
	private $secretValidator;

	/**
	 * @param Registry $registry
	 * @param callable|null $secretValidator
	 */
	public function __construct(Registry $registry, callable $secretValidator = null)
	{
		$this->registry = $registry;
		$this->secretValidator = $secretValidator ?: function ($expected, $actual) { return hash_equals($expected, $actual); };
	}

	/**
	 * @param string $clientIdentifier
	 * @param string $grantType
	 * @param string|null $clientSecret
	 * @param bool $mustValidateSecret
	 * @return ClientEntity|null
	 */
	public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
	{
		/** @var ClientEntity|null $clientEntity */
		$clientEntity = $this->registry->getManager()->getRepository(ClientEntity::class)->fetchOne($this->createQuery()->byIdentifier($clientIdentifier));
		return $clientEntity
			&& $clientEntity->getSecret() !== null
			&& $mustValidateSecret
			&& !call_user_func($this->secretValidator, $clientEntity->getSecret(), $clientSecret)
			? null
			: $clientEntity;
	}

	/**
	 * @return QueryObject
	 */
	protected function createQuery(): QueryObject
	{
		return new ClientQuery();
	}
}
