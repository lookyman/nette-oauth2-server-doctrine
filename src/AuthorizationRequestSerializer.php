<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine;

use Doctrine\ORM\EntityManager;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Lookyman\NetteOAuth2Server\Storage\IAuthorizationRequestSerializer;

class AuthorizationRequestSerializer implements IAuthorizationRequestSerializer
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;

	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @param AuthorizationRequest $authorizationRequest
	 * @return string
	 */
	public function serialize(AuthorizationRequest $authorizationRequest): string
	{
		if ($authorizationRequest->getClient()) {
			$this->entityManager->detach($authorizationRequest->getClient());
		}
		foreach ($authorizationRequest->getScopes() as $scope) {
			$this->entityManager->detach($scope);
		}
		return serialize($authorizationRequest);
	}

	/**
	 * @param string $data
	 * @return AuthorizationRequest
	 */
	public function unserialize(string $data): AuthorizationRequest
	{
		/** @var AuthorizationRequest $authorizationRequest */
		$authorizationRequest = unserialize($data);
		if ($authorizationRequest->getClient()) {
			$authorizationRequest->setClient($this->entityManager->merge($authorizationRequest->getClient()));
		}
		$scopes = [];
		foreach ($authorizationRequest->getScopes() as $scope) {
			$scopes[] = $this->entityManager->merge($scope);
		}
		$authorizationRequest->setScopes($scopes);
		return $authorizationRequest;
	}
}
