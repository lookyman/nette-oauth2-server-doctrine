<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Client;

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

	public function __construct(Registry $registry, ?callable $secretValidator = null)
	{
		$this->registry = $registry;
		$this->secretValidator = $secretValidator ?: function ($expected, $actual) {
			return hash_equals($expected, $actual);
		};
	}

	/**
	 * @param string $clientIdentifier
	 * @param string|null $grantType
	 * @param string|null $clientSecret
	 * @param bool $mustValidateSecret
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getClientEntity($clientIdentifier, $grantType = null, $clientSecret = null, $mustValidateSecret = true): ?ClientEntity
	{
		/** @var ClientEntity|null $clientEntity */
		$clientEntity = $this->registry->getManager()->getRepository(ClientEntity::class)->fetchOne($this->createQuery()->byIdentifier($clientIdentifier));
		return $clientEntity !== null
			&& $mustValidateSecret
			&& $clientEntity->getSecret() !== null
			&& !call_user_func($this->secretValidator, $clientEntity->getSecret(), $clientSecret)
			? null
			: $clientEntity;
	}

	protected function createQuery(): ClientQuery
	{
		return new ClientQuery();
	}

}
