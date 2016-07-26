<?php

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\AuthCode;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;

class AuthCodeEntityTest extends \PHPUnit_Framework_TestCase
{
	public function testDefaults()
	{
		$entity = new AuthCodeEntity();
		self::assertFalse($entity->isRevoked());
		self::assertInternalType('array', $entity->getScopes());
		self::assertCount(0, $entity->getScopes());
	}

	public function testRevoked()
	{
		$entity = new AuthCodeEntity();
		$entity->setRevoked(true);
		self::assertTrue($entity->isRevoked());
	}

	public function testRedirectUri()
	{
		$entity = new AuthCodeEntity();
		$entity->setRedirectUri('uri');
		self::assertEquals('uri', $entity->getRedirectUri());
	}

	public function testIdentifier()
	{
		$entity = new AuthCodeEntity();
		$entity->setIdentifier('ident');
		self::assertEquals('ident', $entity->getIdentifier());
	}

	public function testExpiryDateTime()
	{
		$entity = new AuthCodeEntity();
		$entity->setExpiryDateTime($dateTime = new \DateTime());
		self::assertSame($dateTime, $entity->getExpiryDateTime());
	}

	public function testUserIdentifier()
	{
		$entity = new AuthCodeEntity();
		$entity->setUserIdentifier('user');
		self::assertEquals('user', $entity->getUserIdentifier());
	}

	public function testClient()
	{
		$entity = new AuthCodeEntity();
		$entity->setClient($client = new ClientEntity());
		self::assertSame($client, $entity->getClient());
	}

	public function testScope()
	{
		$entity = new AuthCodeEntity();
		$entity->addScope($scope = new ScopeEntity());
		self::assertInternalType('array', $scopes = $entity->getScopes());
		self::assertCount(1, $scopes);
		self::assertSame($scope, array_pop($scopes));
	}
}
