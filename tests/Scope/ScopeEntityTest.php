<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Scope;

use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;

class ScopeEntityTest extends \PHPUnit_Framework_TestCase
{
	public function testIdentifier()
	{
		$entity = new ScopeEntity();
		$entity->setIdentifier('identifier');
		self::assertEquals('identifier', $entity->getIdentifier());
	}

	public function testJsonSerialize()
	{
		$entity = new ScopeEntity();
		$entity->setIdentifier('identifier');
		self::assertEquals('"identifier"', json_encode($entity));
	}
}
