<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Client;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Doctrine\Registry;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientQuery;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Tests\Mock\ClientRepositoryMock;

class ClientRepositoryTest extends \PHPUnit_Framework_TestCase
{
	public function testGetClientEntityPublic()
	{
		$client = new ClientEntity();
		$client->setSecret('secret');

		$query = $this->getMockBuilder(ClientQuery::class)->disableOriginalConstructor()->getMock();
		$query->expects(self::once())->method('byIdentifier')->with('id')->willReturn($query);

		$entityRepo = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('fetchOne')->with($query)->willReturn($client);

		$manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(ClientEntity::class)->willReturn($entityRepo);

		$registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
		$registry->expects(self::once())->method('getManager')->willReturn($manager);

		$called = false;
		$repository = new ClientRepositoryMock($query, $registry, function () use (&$called) { $called = true; });
		self::assertSame($client, $repository->getClientEntity('id', 'grant', 'secret', false));
		self::assertFalse($called);
	}

	public function testGetClientEntityPrivateSuccess()
	{
		$client = new ClientEntity();
		$client->setSecret('secret');

		$query = $this->getMockBuilder(ClientQuery::class)->disableOriginalConstructor()->getMock();
		$query->expects(self::once())->method('byIdentifier')->with('id')->willReturn($query);

		$entityRepo = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('fetchOne')->with($query)->willReturn($client);

		$manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(ClientEntity::class)->willReturn($entityRepo);

		$registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
		$registry->expects(self::once())->method('getManager')->willReturn($manager);

		$repository = new ClientRepositoryMock($query, $registry);
		self::assertSame($client, $repository->getClientEntity('id', 'grant', 'secret', true));
	}

	public function testGetClientEntityPrivateFail()
	{
		$client = new ClientEntity();
		$client->setSecret('secret');

		$query = $this->getMockBuilder(ClientQuery::class)->disableOriginalConstructor()->getMock();
		$query->expects(self::once())->method('byIdentifier')->with('id')->willReturn($query);

		$entityRepo = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
		$entityRepo->expects(self::once())->method('fetchOne')->with($query)->willReturn($client);

		$manager = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
		$manager->expects(self::once())->method('getRepository')->with(ClientEntity::class)->willReturn($entityRepo);

		$registry = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
		$registry->expects(self::once())->method('getManager')->willReturn($manager);

		$repository = new ClientRepositoryMock($query, $registry, function () { return false; });
		self::assertNull($repository->getClientEntity('id', 'grant', 'secret', true));
	}

	public function testCreateQuery()
	{
		$repository = new ClientRepositoryMock(
			$this->getMockBuilder(QueryObject::class)->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock()
		);
		self::assertInstanceOf(ClientQuery::class, $repository->createQueryOriginal());
	}
}
