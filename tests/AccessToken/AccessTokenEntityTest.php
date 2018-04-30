<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\AccessToken;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;
use PHPUnit\Framework\TestCase;

class AccessTokenEntityTest extends TestCase
{

	public function testDefaults(): void
	{
		$entity = new AccessTokenEntity();
		self::assertFalse($entity->isRevoked());
		self::assertInternalType('array', $entity->getScopes());
		self::assertCount(0, $entity->getScopes());

		self::assertNull($entity->getId());
		$ref = new \ReflectionProperty($entity, 'id');
		$ref->setAccessible(true);
		$ref->setValue($entity, 1);
		self::assertEquals(1, $entity->getId());

		$cloned = clone $entity;
		self::assertNull($cloned->getId());
	}

	public function testRevoked(): void
	{
		$entity = new AccessTokenEntity();
		$entity->setRevoked(true);
		self::assertTrue($entity->isRevoked());
	}

	public function testClient(): void
	{
		$entity = new AccessTokenEntity();
		$entity->setClient($client = new ClientEntity());
		self::assertSame($client, $entity->getClient());
	}

	public function testExpiryDateTime(): void
	{
		$entity = new AccessTokenEntity();
		$entity->setExpiryDateTime($dateTime = new \DateTime());
		self::assertSame($dateTime, $entity->getExpiryDateTime());
	}

	public function testUserIdentifier(): void
	{
		$entity = new AccessTokenEntity();
		$entity->setUserIdentifier('user');
		self::assertEquals('user', $entity->getUserIdentifier());
	}

	public function testScope(): void
	{
		$entity = new AccessTokenEntity();
		$entity->addScope($scope = new ScopeEntity());
		self::assertInternalType('array', $scopes = $entity->getScopes());
		self::assertCount(1, $scopes);
		self::assertSame($scope, array_pop($scopes));
	}

	public function testIdentifier(): void
	{
		$entity = new AccessTokenEntity();
		$entity->setIdentifier('ident');
		self::assertEquals('ident', $entity->getIdentifier());
	}

}
