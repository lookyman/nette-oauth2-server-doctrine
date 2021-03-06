<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\RefreshToken;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenEntity;
use PHPUnit\Framework\TestCase;

class RefreshTokenEntityTest extends TestCase
{

	public function testDefaults(): void
	{
		$entity = new RefreshTokenEntity();
		self::assertFalse($entity->isRevoked());

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
		$entity = new RefreshTokenEntity();
		$entity->setRevoked(true);
		self::assertTrue($entity->isRevoked());
	}

	public function testIdentifier(): void
	{
		$entity = new RefreshTokenEntity();
		$entity->setIdentifier('ident');
		self::assertEquals('ident', $entity->getIdentifier());
	}

	public function testExpiryDateTime(): void
	{
		$entity = new RefreshTokenEntity();
		$entity->setExpiryDateTime($dateTime = new \DateTime());
		self::assertSame($dateTime, $entity->getExpiryDateTime());
	}

	public function testAccessToken(): void
	{
		$entity = new RefreshTokenEntity();
		$entity->setAccessToken($token = new AccessTokenEntity());
		self::assertSame($token, $entity->getAccessToken());
	}

}
