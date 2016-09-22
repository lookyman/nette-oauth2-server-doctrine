<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Kdyby\Doctrine\Events;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\TablePrefixSubscriber;

class TablePrefixListenerTest extends \PHPUnit_Framework_TestCase
{
	public function testGetSubscribedEvents()
	{
		$listener = new TablePrefixSubscriber('');
		self::assertEquals([Events::loadClassMetadata], $listener->getSubscribedEvents());
	}

	/**
	 * @dataProvider loadClassMetadataProvider
	 * @param string $entityName
	 * @param array $associationMappings
	 */
	public function testLoadClassMetadata(string $entityName, array $associationMappings)
	{
		$metadata = new ClassMetadata($entityName);
		$metadata->table['name'] = 'table';
		$metadata->associationMappings = $associationMappings;

		$eventArgs = $this->getMockBuilder(LoadClassMetadataEventArgs::class)->disableOriginalConstructor()->getMock();
		$eventArgs->expects(self::once())->method('getClassMetadata')->willReturn($metadata);

		$listener = new TablePrefixSubscriber('prefix_');
		$listener->loadClassMetadata($eventArgs);

		self::assertEquals(in_array($entityName, TablePrefixSubscriber::ENTITIES) ? 'prefix_table' : 'table', $metadata->table['name']);
		if (isset($metadata->associationMappings['foo']['targetEntity'])
			&& in_array($metadata->associationMappings['foo']['targetEntity'], TablePrefixSubscriber::ENTITIES)
		) {
			self::assertEquals('prefix_join_table', $metadata->associationMappings['foo']['joinTable']['name']);
		}
	}

	/**
	 * @return array
	 */
	public function loadClassMetadataProvider(): array
	{
		return [
			[
				AccessTokenEntity::class,
				[
					'foo' => [
						'type' => ClassMetadataInfo::MANY_TO_MANY,
						'isOwningSide' => true,
						'targetEntity' => ScopeEntity::class,
						'joinTable' => [
							'name' => 'join_table',
						],
					],
				],
			],
			[AuthCodeEntity::class, []],
			[ClientEntity::class, []],
			[RefreshTokenEntity::class, []],
			[ScopeEntity::class, []],
			[\stdClass::class, []],
			[
				\stdClass::class,
				[
					'foo' => [
						'type' => ClassMetadataInfo::MANY_TO_MANY,
						'isOwningSide' => true,
						'targetEntity' => ScopeEntity::class,
						'joinTable' => [
							'name' => 'join_table',
						],
					],
				],
			],
		];
	}
}
