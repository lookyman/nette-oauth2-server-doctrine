<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\Registry;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthorizationRequestSerializer;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;
use Lookyman\NetteOAuth2Server\User\UserEntity;
use PHPUnit\Framework\TestCase;

class AuthorizationRequestSerializerTest extends TestCase
{

	public function testProcess(): void
	{
		$original = new AuthorizationRequest();
		$original->setGrantTypeId('grant');
		$original->setClient($client = new ClientEntity());
		$original->setUser(new UserEntity('user'));
		$original->setScopes([$scope = new ScopeEntity()]);
		$original->setAuthorizationApproved(true);
		$original->setRedirectUri('uri');
		$original->setState('state');
		$original->setCodeChallenge('cc');
		$original->setCodeChallengeMethod('ccm');

		$manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::at(0))->method('detach');
		$manager->expects(self::at(1))->method('detach');
		$manager->expects(self::at(2))->method('merge')->with($client)->willReturn($client);
		$manager->expects(self::at(3))->method('merge')->with($scope)->willReturn($scope);

		$registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
		$registry->expects(self::exactly(2))->method('getManager')->willReturn($manager);

		$serializer = new AuthorizationRequestSerializer($registry);
		$processed = $serializer->unserialize($serializer->serialize($original));
		self::assertEquals($original, $processed);
	}

}
