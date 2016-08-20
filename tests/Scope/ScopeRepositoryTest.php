<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Scope;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\QueryObject;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeRepository;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock\ScopeRepositoryMock;

class ScopeRepositoryTest extends \PHPUnit_Framework_TestCase
{
	public function testGetScopeEntityByIdentifier()
	{
		$scope = new ScopeEntity();

		$query = $this->getMockBuilder(ScopeQuery::class)->disableOriginalConstructor()->getMock();
		$query->expects(self::once())->method('byIdentifier')->with('id')->willReturn($query);

		$entityRepo = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('fetchOne')->with($query)->willReturn($scope);

		$manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(ScopeEntity::class)->willReturn($entityRepo);

		$repository = new ScopeRepositoryMock($query, $manager);
		self::assertSame($scope, $repository->getScopeEntityByIdentifier('id'));
	}

	public function testFinalizeScopes()
	{
		$repository = new ScopeRepository($this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock());
		$scopes = $repository->finalizeScopes([$scope = new ScopeEntity()], 'grant', new ClientEntity(), 'uid');

		self::assertInternalType('array', $scopes);
		self::assertCount(1, $scopes);
		self::assertSame($scope, array_pop($scopes));
	}

	public function testCreateQuery()
	{
		$repository = new ScopeRepositoryMock(
			$this->getMockBuilder(QueryObject::class)->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock()
		);
		self::assertInstanceOf(ScopeQuery::class, $repository->createQueryOriginal());
	}
}
