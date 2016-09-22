<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\AuthCode;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\Registry;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeRepository;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock\AuthCodeRepositoryMock;

class AuthCodeRepositoryTest extends \PHPUnit_Framework_TestCase
{
	public function testGetNewAuthCode()
	{
		$repository = new AuthCodeRepository($this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock());
		self::assertInstanceOf(AuthCodeEntity::class, $repository->getNewAuthCode());
	}

	public function testPersistNewAuthCode()
	{
		$code = new AuthCodeEntity();

		$manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('persist')->with($code);
		$manager->expects(self::once())->method('flush');

		$registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
		$registry->expects(self::once())->method('getManager')->willReturn($manager);

		$repository = new AuthCodeRepository($registry);
		$repository->persistNewAuthCode($code);
	}

	public function testRevokeAuthCode()
	{
		$code = new AuthCodeEntity();

		$query = $this->getMockBuilder(AuthCodeQuery::class)->disableOriginalConstructor()->getMock();
		$query->expects(self::once())->method('byIdentifier')->with('id')->willReturn($query);

		$entityRepo = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('fetchOne')->with($query)->willReturn($code);

		$manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(AuthCodeEntity::class)->willReturn($entityRepo);
		$manager->expects(self::once())->method('flush');

		$registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
		$registry->expects(self::once())->method('getManager')->willReturn($manager);

		$repository = new AuthCodeRepositoryMock($query, $registry);
		$repository->revokeAuthCode('id');

		self::assertTrue($code->isRevoked());
	}

	public function testIsAuthCodeRevoked()
	{
		$code = new AuthCodeEntity();
		$code->setRevoked(true);

		$query = $this->getMockBuilder(AuthCodeQuery::class)->disableOriginalConstructor()->getMock();
		$query->expects(self::once())->method('byIdentifier')->with('id')->willReturn($query);

		$entityRepo = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('fetchOne')->with($query)->willReturn($code);

		$manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(AuthCodeEntity::class)->willReturn($entityRepo);

		$registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
		$registry->expects(self::once())->method('getManager')->willReturn($manager);

		$repository = new AuthCodeRepositoryMock($query, $registry);
		self::assertTrue($repository->isAuthCodeRevoked('id'));
	}

	public function testCreateQuery()
	{
		$repository = new AuthCodeRepositoryMock(
			$this->getMockBuilder(AuthCodeQuery::class)->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock()
		);
		self::assertInstanceOf(AuthCodeQuery::class, $repository->createQueryOriginal());
	}
}
