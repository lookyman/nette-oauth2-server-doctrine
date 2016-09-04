<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine;

use Kdyby\Doctrine\Registry;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Lookyman\NetteOAuth2Server\Storage\IAuthorizationRequestSerializer;

class AuthorizationRequestSerializer implements IAuthorizationRequestSerializer
{
	/**
	 * @var Registry
	 */
	private $registry;

	/**
	 * @param Registry $registry
	 */
	public function __construct(Registry $registry)
	{
		$this->registry = $registry;
	}

	/**
	 * @param AuthorizationRequest $authorizationRequest
	 * @return string
	 */
	public function serialize(AuthorizationRequest $authorizationRequest): string
	{
		$manager = $this->registry->getManager();
		if ($authorizationRequest->getClient()) {
			$manager->detach($authorizationRequest->getClient());
		}
		foreach ($authorizationRequest->getScopes() as $scope) {
			$manager->detach($scope);
		}
		return serialize($authorizationRequest);
	}

	/**
	 * @param string $data
	 * @return AuthorizationRequest
	 */
	public function unserialize(string $data): AuthorizationRequest
	{
		$manager = $this->registry->getManager();
		/** @var AuthorizationRequest $authorizationRequest */
		$authorizationRequest = unserialize($data);
		if ($client = $authorizationRequest->getClient()) {
			$authorizationRequest->setClient($manager->merge($client));
		}
		$scopes = [];
		foreach ($authorizationRequest->getScopes() as $scope) {
			$scopes[] = $manager->merge($scope);
		}
		$authorizationRequest->setScopes($scopes);
		return $authorizationRequest;
	}
}
