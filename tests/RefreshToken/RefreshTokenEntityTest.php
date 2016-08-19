<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\RefreshToken;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenEntity;

class RefreshTokenEntityTest extends \PHPUnit_Framework_TestCase
{
	public function testDefaults()
	{
		$entity = new RefreshTokenEntity();
		self::assertFalse($entity->isRevoked());
	}

	public function testRevoked()
	{
		$entity = new RefreshTokenEntity();
		$entity->setRevoked(true);
		self::assertTrue($entity->isRevoked());
	}

	public function testIdentifier()
	{
		$entity = new RefreshTokenEntity();
		$entity->setIdentifier('ident');
		self::assertEquals('ident', $entity->getIdentifier());
	}

	public function testExpiryDateTime()
	{
		$entity = new RefreshTokenEntity();
		$entity->setExpiryDateTime($dateTime = new \DateTime());
		self::assertSame($dateTime, $entity->getExpiryDateTime());
	}

	public function testAccessToken()
	{
		$entity = new RefreshTokenEntity();
		$entity->setAccessToken($token = new AccessTokenEntity());
		self::assertSame($token, $entity->getAccessToken());
	}
}
