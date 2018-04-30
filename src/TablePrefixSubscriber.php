<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Kdyby\Doctrine\Events;
use Kdyby\Doctrine\Mapping\ClassMetadata;
use Kdyby\Events\Subscriber;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;

class TablePrefixSubscriber implements Subscriber
{

	public const DEFAULT_PREFIX = 'nette_oauth2_server_';

	public const ENTITIES = [
		AccessTokenEntity::class,
		AuthCodeEntity::class,
		ClientEntity::class,
		RefreshTokenEntity::class,
		ScopeEntity::class,
	];

	/**
	 * @var string
	 */
	private $prefix;

	public function __construct(string $prefix = self::DEFAULT_PREFIX)
	{
		$this->prefix = $prefix;
	}

	public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
	{
		/** @var ClassMetadata $metadata */
		$metadata = $eventArgs->getClassMetadata();
		if (in_array($metadata->getName(), self::ENTITIES, true)) {
			$metadata->setPrimaryTable([
				'name' => self::getPrefixedName($this->prefix, $metadata->getTableName()),
			]);
		}
		foreach ($metadata->getAssociationMappings() as $name => $mapping) {
			if ($mapping['type'] === ClassMetadataInfo::MANY_TO_MANY
				&& $mapping['isOwningSide']
				&& in_array($mapping['targetEntity'], self::ENTITIES, true)
			) {
				$metadata->associationMappings[$name]['joinTable']['name'] = self::getPrefixedName($this->prefix, $mapping['joinTable']['name']);
			}
		}
	}

	/**
	 * @return string[]
	 */
	public function getSubscribedEvents(): array
	{
		return [Events::loadClassMetadata];
	}

	protected static function getPrefixedName(string $prefix, string $name): string
	{
		return $prefix . $name;
	}

}
