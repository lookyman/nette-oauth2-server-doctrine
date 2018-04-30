<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\RefreshToken;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\Registry;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken\RefreshTokenRepository;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock\RefreshTokenRepositoryMock;
use PHPUnit\Framework\TestCase;

class RefreshTokenRepositoryTest extends TestCase
{

	public function testGetNewRefreshToken(): void
	{
		$repository = new RefreshTokenRepository($this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock());
		self::assertInstanceOf(RefreshTokenEntity::class, $repository->getNewRefreshToken());
	}

	public function testPersistNewRefreshToken(): void
	{
		$token = new RefreshTokenEntity();

		$manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('persist')->with($token);
		$manager->expects(self::once())->method('flush');

		$registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
		$registry->expects(self::once())->method('getManager')->willReturn($manager);

		$repository = new RefreshTokenRepository($registry);
		$repository->persistNewRefreshToken($token);
	}

	public function testRevokeRefreshToken(): void
	{
		$token = new RefreshTokenEntity();

		$query = $this->getMockBuilder(RefreshTokenQuery::class)->disableOriginalConstructor()->getMock();
		$query->expects(self::once())->method('byIdentifier')->with('id')->willReturn($query);

		$entityRepo = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('fetchOne')->with($query)->willReturn($token);

		$manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(RefreshTokenEntity::class)->willReturn($entityRepo);
		$manager->expects(self::once())->method('flush');

		$registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
		$registry->expects(self::once())->method('getManager')->willReturn($manager);

		$repository = new RefreshTokenRepositoryMock($query, $registry);
		$repository->revokeRefreshToken('id');

		self::assertTrue($token->isRevoked());
	}

	public function testIsRefreshTokenRevoked(): void
	{
		$token = new RefreshTokenEntity();
		$token->setRevoked(true);

		$query = $this->getMockBuilder(RefreshTokenQuery::class)->disableOriginalConstructor()->getMock();
		$query->expects(self::once())->method('byIdentifier')->with('id')->willReturn($query);

		$entityRepo = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('fetchOne')->with($query)->willReturn($token);

		$manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(RefreshTokenEntity::class)->willReturn($entityRepo);

		$registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
		$registry->expects(self::once())->method('getManager')->willReturn($manager);

		$repository = new RefreshTokenRepositoryMock($query, $registry);
		self::assertTrue($repository->isRefreshTokenRevoked('id'));
	}

	public function testCreateQuery(): void
	{
		$repository = new RefreshTokenRepositoryMock(
			$this->getMockBuilder(RefreshTokenQuery::class)->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock()
		);
		self::assertInstanceOf(RefreshTokenQuery::class, $repository->createQueryOriginal());
	}

}
