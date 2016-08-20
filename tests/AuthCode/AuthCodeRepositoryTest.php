<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\AuthCode;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\QueryObject;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode\AuthCodeRepository;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock\AuthCodeRepositoryMock;

class AuthCodeRepositoryTest extends \PHPUnit_Framework_TestCase
{
	public function testGetNewAuthCode()
	{
		$repository = new AuthCodeRepository($this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock());
		self::assertInstanceOf(AuthCodeEntity::class, $repository->getNewAuthCode());
	}

	public function testPersistNewAuthCode()
	{
		$code = new AuthCodeEntity();

		$manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('persist')->with($code);
		$manager->expects(self::once())->method('flush');

		$repository = new AuthCodeRepository($manager);
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

		$repository = new AuthCodeRepositoryMock($query, $manager);
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

		$repository = new AuthCodeRepositoryMock($query, $manager);
		self::assertTrue($repository->isAuthCodeRevoked('id'));
	}

	public function testCreateQuery()
	{
		$repository = new AuthCodeRepositoryMock(
			$this->getMockBuilder(QueryObject::class)->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock()
		);
		self::assertInstanceOf(AuthCodeQuery::class, $repository->createQueryOriginal());
	}
}
