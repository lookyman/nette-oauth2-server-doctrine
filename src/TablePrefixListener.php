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

class TablePrefixListener implements Subscriber
{
	const ENTITIES = [
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

	/**
	 * @param string $prefix
	 */
	public function __construct(string $prefix)
	{
		$this->prefix = $prefix;
	}

	/**
	 * @param LoadClassMetadataEventArgs $eventArgs
	 */
	public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
	{
		/** @var ClassMetadata $metadata */
		$metadata = $eventArgs->getClassMetadata();
		if (in_array($metadata->getName(), self::ENTITIES)) {
			$metadata->setPrimaryTable([
				'name' => self::getPrefixedName($this->prefix, $metadata->getTableName()),
			]);
		}
		foreach ($metadata->getAssociationMappings() as $name => $mapping) {
			if ($mapping['type'] === ClassMetadataInfo::MANY_TO_MANY
				&& $mapping['isOwningSide']
				&& in_array($mapping['targetEntity'], self::ENTITIES)
			) {
				$metadata->associationMappings[$name]['joinTable']['name'] = self::getPrefixedName($this->prefix, $mapping['joinTable']['name']);
			}
		}
	}

	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return [Events::loadClassMetadata];
	}

	/**
	 * @param string $prefix
	 * @param string $name
	 * @return string
	 */
	protected static function getPrefixedName(string $prefix, string $name): string
	{
		return $prefix . $name;
	}
}
