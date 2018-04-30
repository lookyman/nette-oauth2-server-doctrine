<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\AuthCode;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;
use PHPUnit\Framework\TestCase;

class AuthCodeEntityTest extends TestCase
{

	public function testDefaults(): void
	{
		$entity = new AuthCodeEntity();
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
		$entity = new AuthCodeEntity();
		$entity->setRevoked(true);
		self::assertTrue($entity->isRevoked());
	}

	public function testRedirectUri(): void
	{
		$entity = new AuthCodeEntity();
		$entity->setRedirectUri('uri');
		self::assertEquals('uri', $entity->getRedirectUri());
	}

	public function testIdentifier(): void
	{
		$entity = new AuthCodeEntity();
		$entity->setIdentifier('ident');
		self::assertEquals('ident', $entity->getIdentifier());
	}

	public function testExpiryDateTime(): void
	{
		$entity = new AuthCodeEntity();
		$entity->setExpiryDateTime($dateTime = new \DateTime());
		self::assertSame($dateTime, $entity->getExpiryDateTime());
	}

	public function testUserIdentifier(): void
	{
		$entity = new AuthCodeEntity();
		$entity->setUserIdentifier('user');
		self::assertEquals('user', $entity->getUserIdentifier());
	}

	public function testClient(): void
	{
		$entity = new AuthCodeEntity();
		$entity->setClient($client = new ClientEntity());
		self::assertSame($client, $entity->getClient());
	}

	public function testScope(): void
	{
		$entity = new AuthCodeEntity();
		$entity->addScope($scope = new ScopeEntity());
		self::assertInternalType('array', $scopes = $entity->getScopes());
		self::assertCount(1, $scopes);
		self::assertSame($scope, array_pop($scopes));
	}

}
