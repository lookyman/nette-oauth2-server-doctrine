<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Scope;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\Registry;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeRepository;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock\ScopeRepositoryMock;
use PHPUnit\Framework\TestCase;

class ScopeRepositoryTest extends TestCase
{

	public function testGetScopeEntityByIdentifier(): void
	{
		$scope = new ScopeEntity();

		$query = $this->getMockBuilder(ScopeQuery::class)->disableOriginalConstructor()->getMock();
		$query->expects(self::once())->method('byIdentifier')->with('id')->willReturn($query);

		$entityRepo = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('fetchOne')->with($query)->willReturn($scope);

		$manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(ScopeEntity::class)->willReturn($entityRepo);

		$registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
		$registry->expects(self::once())->method('getManager')->willReturn($manager);

		$repository = new ScopeRepositoryMock($query, $registry);
		self::assertSame($scope, $repository->getScopeEntityByIdentifier('id'));
	}

	public function testFinalizeScopes(): void
	{
		$repository = new ScopeRepository($this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock());
		$scopes = $repository->finalizeScopes([$scope = new ScopeEntity()], 'grant', new ClientEntity(), 'uid');

		self::assertInternalType('array', $scopes);
		self::assertCount(1, $scopes);
		self::assertSame($scope, array_pop($scopes));
	}

	public function testCreateQuery(): void
	{
		$repository = new ScopeRepositoryMock(
			$this->getMockBuilder(ScopeQuery::class)->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock()
		);
		self::assertInstanceOf(ScopeQuery::class, $repository->createQueryOriginal());
	}

}
