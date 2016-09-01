<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Client;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;

class ClientEntityTest extends \PHPUnit_Framework_TestCase
{
	public function testDefaults()
	{
		$entity = new ClientEntity();

		self::assertNull($entity->getId());
		$ref = new \ReflectionProperty($entity, 'id');
		$ref->setAccessible(true);
		$ref->setValue($entity, 1);
		self::assertEquals(1, $entity->getId());

		$cloned = clone $entity;
		self::assertNull($cloned->getId());
	}

	public function testSecret()
	{
		$entity = new ClientEntity();
		$entity->setSecret('secret');
		self::assertEquals('secret', $entity->getSecret());
	}

	public function testIdentifier()
	{
		$entity = new ClientEntity();
		$entity->setIdentifier('identifier');
		self::assertEquals('identifier', $entity->getIdentifier());
	}

	public function testName()
	{
		$entity = new ClientEntity();
		$entity->setName('name');
		self::assertEquals('name', $entity->getName());
	}

	public function testRedirectUri()
	{
		$entity = new ClientEntity();
		$entity->setRedirectUri('uri');
		self::assertEquals('uri', $entity->getRedirectUri());
	}
}
