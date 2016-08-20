<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine;

use Kdyby\Doctrine\DI\IEntityProvider;
use Kdyby\Events\DI\EventsExtension;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;
use Lookyman\NetteOAuth2Server\RedirectConfig;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenRepository;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeRepository;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientRepository;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenRepository;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeRepository;
use Lookyman\NetteOAuth2Server\Storage\IAuthorizationRequestSerializer;
use Lookyman\NetteOAuth2Server\UI\ApproveControlFactory;
use Lookyman\NetteOAuth2Server\UI\OAuth2Presenter;
use Lookyman\NetteOAuth2Server\User\UserRepository;
use Nette\Application\IPresenterFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Statement;
use Nette\Utils\Validators;

class NetteOAuth2ServerDoctrineExtension extends CompilerExtension implements IEntityProvider
{
	/**
	 * @var array
	 */
	private $defaults = [
		'grants' => [
			'authCode' => false,
			'clientCredentials' => false,
			'implicit' => false,
			'password' => false,
			'refreshToken' => false,
		],
		'privateKey' => null,
		'publicKey' => null,
		'approveDestination' => null,
		'loginDestination' => null,
		'tablePrefix' => 'nette_oauth2_server_',
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);

		// Table mapping
		Validators::assertField($config, 'tablePrefix', 'string');
		$builder->addDefinition($this->prefix('tablePrefixListener'))
			->setClass(TablePrefixListener::class, [$config['tablePrefix']])
			->addTag(EventsExtension::TAG_SUBSCRIBER);

		// Common repositories
		$builder->addDefinition($this->prefix('repository.client'))
			->setClass(ClientRepository::class);
		$builder->addDefinition($this->prefix('repository.accessToken'))
			->setClass(AccessTokenRepository::class);
		$builder->addDefinition($this->prefix('repository.scope'))
			->setClass(ScopeRepository::class);

		// Private & public keys
		Validators::assertField($config, 'publicKey', 'string');
		Validators::assertField($config, 'privateKey', 'string|array');
		if (is_array($config['privateKey'])) {
			Validators::assertField($config['privateKey'], 'kayPath', 'string');
			Validators::assertField($config['privateKey'], 'passPhrase', 'string');
			$privateKey = new Statement(CryptKey::class, [$config['privateKey']['keyPath'], $config['privateKey']['passPhrase']]);

		} else {
			$privateKey = $config['privateKey'];
		}

		// Authorization & resource server
		$authorizationServer = $builder->addDefinition($this->prefix('authorizationServer'))
			->setClass(AuthorizationServer::class, [
				'privateKey' => $privateKey,
				'publicKey' => $config['publicKey'],
			]);
		$builder->addDefinition($this->prefix('resourceServer'))
			->setClass(ResourceServer::class, [
				'publicKey' => $config['publicKey'],
			]);

		$enableAuthorizeEndpoint = false;

		// Grants
		Validators::assertField($config, 'grants', 'array');
		foreach ($config['grants'] as $grant => $options) {
			Validators::assert($options, 'boolean|array');
			if ($options === false) {
				continue;

			} else {
				$options = (array) $options;
			}

			$definition = $builder->addDefinition($this->prefix('grant.' . $grant));

			switch ($grant) {
				case 'authCode':
					if (!array_key_exists('authCodeTtl', $options)) {
						$options['authCodeTtl'] = 'PT10M';
					}
					$definition->setClass(AuthCodeGrant::class, ['authCodeTTL' => $this->createDateIntervalStatement($options['authCodeTtl'])]);
					$builder->addDefinition($this->prefix('repository.authCode'))
						->setClass(AuthCodeRepository::class);
					$this->registerRefreshTokenRepository($builder);
					$this->registerUserRepository($builder);
					$enableAuthorizeEndpoint = true;
					break;
				case 'clientCredentials':
					$definition->setClass(ClientCredentialsGrant::class);
					break;
				case 'implicit':
					if (!array_key_exists('accessTokenTtl', $options)) {
						$options['accessTokenTtl'] = 'PT10M';
					}
					$definition->setClass(ImplicitGrant::class, ['accessTokenTTL' => $this->createDateIntervalStatement($options['accessTokenTtl'])]);
					$this->registerUserRepository($builder);
					$enableAuthorizeEndpoint = true;
					break;
				case 'password':
					$definition->setClass(PasswordGrant::class);
					$this->registerRefreshTokenRepository($builder);
					$this->registerUserRepository($builder);
					break;
				case 'refreshToken':
					$definition->setClass(RefreshTokenGrant::class);
					$this->registerRefreshTokenRepository($builder);
					break;
				default:
					throw new \InvalidArgumentException(sprintf('Unknown grant %s', $grant));
			}

			$args = [$this->prefix('@grant.' . $grant)];
			if (array_key_exists('ttl', $options)) {
				$args[] = $this->createDateIntervalStatement($options['ttl']);
			}
			$authorizationServer->addSetup('enableGrantType', $args);
		}

		// Presenter
		$presenter = $builder->addDefinition($this->prefix('presenter'))
			->setClass(OAuth2Presenter::class);

		if ($enableAuthorizeEndpoint) {
			$builder->addDefinition($this->prefix('serializer'))
				->setClass(IAuthorizationRequestSerializer::class)
				->setFactory(AuthorizationRequestSerializer::class);
			$builder->addDefinition($this->prefix('approveControlFactory'))
				->setClass(ApproveControlFactory::class);

			Validators::assertField($config, 'approveDestination', 'string');
			Validators::assertField($config, 'loginDestination', 'string');
			$builder->addDefinition($this->prefix('redirectConfig'))
				->setClass(RedirectConfig::class, [
					'approveDestination' => $config['approveDestination'],
					'loginDestination' => $config['loginDestination'],
				]);

			$presenter->addSetup('setRedirectConfig')
				->addSetup('setAuthorizationRequestSerializer');
		}
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		// Mapping
		$presenterFactory = $builder->getDefinition($builder->getByType(IPresenterFactory::class));
		$presenterFactory->addSetup('if (!? instanceof \Nette\Application\PresenterFactory) { throw new \RuntimeException(\'Cannot set OAuth2Server mapping\'); } else { ?->setMapping(?); }', [
			'@self', '@self', ['NetteOAuth2Server' => 'Lookyman\NetteOAuth2Server\UI\*Presenter']
		]);
	}

	/**
	 * @return array
	 */
	public function getEntityMappings()
	{
		return ['Lookyman\NetteOAuth2Server\Storage\Doctrine' => __DIR__];
	}

	/**
	 * @param ContainerBuilder $builder
	 */
	private function registerRefreshTokenRepository(ContainerBuilder $builder)
	{
		if (!$builder->hasDefinition($this->prefix('repository.refreshToken'))) {
			$builder->addDefinition($this->prefix('repository.refreshToken'))
				->setClass(RefreshTokenRepository::class);
		}
	}

	/**
	 * @param ContainerBuilder $builder
	 */
	private function registerUserRepository(ContainerBuilder $builder)
	{
		if (!$builder->hasDefinition($this->prefix('repository.user'))) {
			$builder->addDefinition($this->prefix('repository.user'))
				->setClass(UserRepository::class);
		}
	}

	/**
	 * @param string $interval
	 * @return Statement
	 * @throws \Exception
	 */
	private function createDateIntervalStatement(string $interval): Statement
	{
		new \DateInterval($interval); // throw early
		return new Statement(\DateInterval::class, [$interval]);
	}
}
