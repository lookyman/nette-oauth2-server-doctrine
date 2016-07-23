<?php

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\AccessToken;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;

class AccessTokenEntityTest extends \PHPUnit_Framework_TestCase
{
	public function testDefaults()
	{
		$entity = new AccessTokenEntity();
		self::assertFalse($entity->isRevoked());
		self::assertInternalType('array', $entity->getScopes());
		self::assertCount(0, $entity->getScopes());
	}

	public function testRevoked()
	{
		$entity = new AccessTokenEntity();
		$entity->setRevoked(true);
		self::assertTrue($entity->isRevoked());
	}

	public function testClient()
	{
		$entity = new AccessTokenEntity();
		$entity->setClient($client = new ClientEntity());
		self::assertSame($client, $entity->getClient());
	}

	public function testExpiryDateTime()
	{
		$entity = new AccessTokenEntity();
		$entity->setExpiryDateTime($dateTime = new \DateTime());
		self::assertSame($dateTime, $entity->getExpiryDateTime());
	}

	public function testUserIdentifier()
	{
		$entity = new AccessTokenEntity();
		$entity->setUserIdentifier('user');
		self::assertEquals('user', $entity->getUserIdentifier());
	}

	public function testScope()
	{
		$entity = new AccessTokenEntity();
		$entity->addScope($scope = new ScopeEntity());
		self::assertCount(1, $scopes = $entity->getScopes());
		self::assertSame($scope, array_pop($scopes));
	}

	public function testIdentifier()
	{
		$entity = new AccessTokenEntity();
		$entity->setIdentifier('ident');
		self::assertEquals('ident', $entity->getIdentifier());
	}
}
