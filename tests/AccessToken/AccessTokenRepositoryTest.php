<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\AccessToken;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\Registry;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenRepository;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock\AccessTokenRepositoryMock;

class AccessTokenRepositoryTest extends \PHPUnit_Framework_TestCase
{
	public function testGetNewToken()
	{
		$repository = new AccessTokenRepository($this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock());
		$token = $repository->getNewToken($client = new ClientEntity(), [$scope = new ScopeEntity()], 'uid');

		self::assertInstanceOf(AccessTokenEntity::class, $token);
		self::assertSame($client, $token->getClient());
		self::assertInternalType('array', $scopes = $token->getScopes());
		self::assertCount(1, $scopes);
		self::assertSame($scope, array_pop($scopes));
		self::assertEquals('uid', $token->getUserIdentifier());
	}

	public function testPersistNewAccessToken()
	{
		$token = new AccessTokenEntity();

		$manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('persist')->with($token);
		$manager->expects(self::once())->method('flush');

		$registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
		$registry->expects(self::once())->method('getManager')->willReturn($manager);

		$repository = new AccessTokenRepository($registry);
		$repository->persistNewAccessToken($token);
	}

	public function testRevokeAccessToken()
	{
		$token = new AccessTokenEntity();

		$query = $this->getMockBuilder(AccessTokenQuery::class)->disableOriginalConstructor()->getMock();
		$query->expects(self::once())->method('byIdentifier')->with('id')->willReturn($query);

		$entityRepo = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('fetchOne')->with($query)->willReturn($token);

		$manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(AccessTokenEntity::class)->willReturn($entityRepo);
		$manager->expects(self::once())->method('flush');

		$registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
		$registry->expects(self::once())->method('getManager')->willReturn($manager);

		$repository = new AccessTokenRepositoryMock($query, $registry);
		$repository->revokeAccessToken('id');

		self::assertTrue($token->isRevoked());
	}

	public function testIsAccessTokenRevoked()
	{
		$token = new AccessTokenEntity();
		$token->setRevoked(true);

		$query = $this->getMockBuilder(AccessTokenQuery::class)->disableOriginalConstructor()->getMock();
		$query->expects(self::once())->method('byIdentifier')->with('id')->willReturn($query);

		$entityRepo = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('fetchOne')->with($query)->willReturn($token);

		$manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(AccessTokenEntity::class)->willReturn($entityRepo);

		$registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
		$registry->expects(self::once())->method('getManager')->willReturn($manager);

		$repository = new AccessTokenRepositoryMock($query, $registry);
		self::assertTrue($repository->isAccessTokenRevoked('id'));
	}

	public function testCreateQuery()
	{
		$repository = new AccessTokenRepositoryMock(
			$this->getMockBuilder(AccessTokenQuery::class)->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock()
		);
		self::assertInstanceOf(AccessTokenQuery::class, $repository->createQueryOriginal());
	}
}
