<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Scope;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;
use PHPUnit\Framework\TestCase;

class ScopeEntityTest extends TestCase
{

	public function testDefaults(): void
	{
		$entity = new ScopeEntity();

		self::assertNull($entity->getId());
		$ref = new \ReflectionProperty($entity, 'id');
		$ref->setAccessible(true);
		$ref->setValue($entity, 1);
		self::assertEquals(1, $entity->getId());

		$cloned = clone $entity;
		self::assertNull($cloned->getId());
	}

	public function testIdentifier(): void
	{
		$entity = new ScopeEntity();
		$entity->setIdentifier('identifier');
		self::assertEquals('identifier', $entity->getIdentifier());
	}

	public function testJsonSerialize(): void
	{
		$entity = new ScopeEntity();
		$entity->setIdentifier('identifier');
		self::assertEquals('"identifier"', json_encode($entity));
	}

}
