<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine;

use Kdyby\Doctrine\Registry;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\IAuthorizationRequestSerializer;

class AuthorizationRequestSerializer implements IAuthorizationRequestSerializer
{

	/**
	 * @var Registry
	 */
	private $registry;

	public function __construct(Registry $registry)
	{
		$this->registry = $registry;
	}

	public function serialize(AuthorizationRequest $authorizationRequest): string
	{
		$manager = $this->registry->getManager();
		/** @var ClientEntity|null $client */
		$client = $authorizationRequest->getClient();
		if ($client !== null) {
			$manager->detach($authorizationRequest->getClient());
		}
		foreach ($authorizationRequest->getScopes() as $scope) {
			$manager->detach($scope);
		}
		return serialize($authorizationRequest);
	}

	public function unserialize(string $data): AuthorizationRequest
	{
		$manager = $this->registry->getManager();
		/** @var AuthorizationRequest $authorizationRequest */
		$authorizationRequest = unserialize($data);
		/** @var ClientEntity|null $client */
		$client = $authorizationRequest->getClient();
		if ($client !== null) {
			/** @var ClientEntity $client */
			$client = $manager->merge($client);
			$authorizationRequest->setClient($client);
		}
		$scopes = [];
		foreach ($authorizationRequest->getScopes() as $scope) {
			$scopes[] = $manager->merge($scope);
		}
		$authorizationRequest->setScopes($scopes);
		return $authorizationRequest;
	}

}
