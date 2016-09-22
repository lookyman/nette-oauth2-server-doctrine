<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests;

use Kdyby\Doctrine\Events;
use Kdyby\Events\DI\EventsExtension;
use Kdyby\Events\EventManager;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\ResourceServer;
use Lookyman\NetteOAuth2Server\RedirectConfig;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthorizationRequestSerializer;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\NetteOAuth2ServerDoctrineExtension;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\TablePrefixSubscriber;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock\CustomGrantMock;
use Lookyman\NetteOAuth2Server\Storage\IAuthorizationRequestSerializer;
use Lookyman\NetteOAuth2Server\UI\ApproveControlFactory;
use Lookyman\NetteOAuth2Server\UI\IApproveControlFactory;
use Lookyman\NetteOAuth2Server\UI\OAuth2Presenter;
use Lookyman\NetteOAuth2Server\User\LoginSubscriber;
use Nette\Configurator;
use Nette\DI\Container;

class NetteOAuth2ServerDoctrineExtensionTest extends \PHPUnit_Framework_TestCase
{
	public function testExtension()
	{
		$container = $this->createContainer();

		/** @var TablePrefixSubscriber $tablePrefixListener */
		$tablePrefixListener = $container->getByType(TablePrefixSubscriber::class);
		$ref = new \ReflectionProperty($tablePrefixListener, 'prefix');
		$ref->setAccessible(true);
		self::assertEquals('test_', $ref->getValue($tablePrefixListener));
		/** @var EventManager $eventManager */
		$eventManager = $container->getByType(EventManager::class);
		$listeners = $eventManager->getListeners(Events::loadClassMetadata);
		self::assertSame($tablePrefixListener, array_pop($listeners));

		$container->getByType(AccessTokenRepositoryInterface::class);
		$container->getByType(AuthCodeRepositoryInterface::class);
		$container->getByType(ClientRepositoryInterface::class);
		$container->getByType(RefreshTokenRepositoryInterface::class);
		$container->getByType(ScopeRepositoryInterface::class);
		$container->getByType(UserRepositoryInterface::class);

		/** @var AuthorizationServer $authorizationServer */
		$authorizationServer = $container->getByType(AuthorizationServer::class);
		$ref = new \ReflectionProperty($authorizationServer, 'privateKey');
		$ref->setAccessible(true);
		self::assertRegExp('#/keys/private\.key$#', $ref->getValue($authorizationServer)->getKeyPath());
		$ref = new \ReflectionProperty($authorizationServer, 'publicKey');
		$ref->setAccessible(true);
		self::assertRegExp('#/keys/public\.key$#', $ref->getValue($authorizationServer)->getKeyPath());

		/** @var ResourceServer $resourceServer */
		$resourceServer = $container->getByType(ResourceServer::class);
		$ref = new \ReflectionProperty($resourceServer, 'publicKey');
		$ref->setAccessible(true);
		self::assertRegExp('#/keys/public\.key$#', $ref->getValue($resourceServer)->getKeyPath());

		$container->getByType(AuthCodeGrant::class);
		$container->getByType(ClientCredentialsGrant::class);
		$container->getByType(ImplicitGrant::class);
		$container->getByType(PasswordGrant::class);
		$container->getByType(RefreshTokenGrant::class);
		$container->getByType(CustomGrantMock::class);

		$ref = new \ReflectionProperty($authorizationServer, 'enabledGrantTypes');
		$ref->setAccessible(true);
		self::assertCount(6, $grants = $ref->getValue($authorizationServer));
		self::assertArrayHasKey('authorization_code', $grants);
		self::assertArrayHasKey('client_credentials', $grants);
		self::assertArrayHasKey('implicit', $grants);
		self::assertArrayHasKey('password', $grants);
		self::assertArrayHasKey('refresh_token', $grants);
		self::assertArrayHasKey('custom', $grants);

		$container->getByType(LoginSubscriber::class);

		$subscribers = $container->findByTag(EventsExtension::TAG_SUBSCRIBER);
		self::assertArrayHasKey('oauth2.loginSubscriber', $subscribers);
		self::assertArrayHasKey('oauth2.tablePrefixSubscriber', $subscribers);
		self::assertInstanceOf(LoginSubscriber::class, $container->getService('oauth2.loginSubscriber'));
		self::assertInstanceOf(TablePrefixSubscriber::class, $container->getService('oauth2.tablePrefixSubscriber'));

		self::assertInstanceOf(OAuth2Presenter::class, $container->getByType(OAuth2Presenter::class));
		self::assertInstanceOf(ApproveControlFactory::class, $container->getByType(IApproveControlFactory::class));
		self::assertInstanceOf(AuthorizationRequestSerializer::class, $container->getByType(IAuthorizationRequestSerializer::class));

		/** @var RedirectConfig $redirectConfig */
		$redirectConfig = $container->getByType(RedirectConfig::class);
		self::assertInstanceOf(RedirectConfig::class, $redirectConfig);
		self::assertEquals(['Foo:bar'], $redirectConfig->getApproveDestination());
		self::assertEquals(['Bar:foo'], $redirectConfig->getLoginDestination());
	}

	public function testGetEntityMappings()
	{
		$extension = new NetteOAuth2ServerDoctrineExtension();
		self::assertArrayHasKey('Lookyman\NetteOAuth2Server\Storage\Doctrine', $extension->getEntityMappings());
	}

	private function createContainer(): Container
	{
		$tempDir = __DIR__ . '/temp';
		if (!@mkdir($tempDir) && !is_dir($tempDir)) {
			throw new \RuntimeException('Cannot create temp directory');
		}

		/** @var \SplFileInfo $entry */
		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tempDir, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $entry) {
			$entry->isDir() ? rmdir((string) $entry) : unlink((string) $entry);
		}

		$configurator = new Configurator();
		$configurator->setTempDirectory($tempDir);
		$configurator->addConfig(__DIR__ . '/config.neon');
		return $configurator->createContainer();
	}
}
