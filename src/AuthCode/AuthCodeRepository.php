<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode;

use Kdyby\Doctrine\Registry;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{

	/**
	 * @var Registry
	 */
	private $registry;

	public function __construct(Registry $registry)
	{
		$this->registry = $registry;
	}

	public function getNewAuthCode(): AuthCodeEntity
	{
		return new AuthCodeEntity();
	}

	public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
	{
		if ($authCodeEntity instanceof AuthCodeEntity) {
			$manager = $this->registry->getManager();
			$manager->persist($authCodeEntity);
			$manager->flush();
		}
	}

	/**
	 * @param string $codeId
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function revokeAuthCode($codeId): void
	{
		$manager = $this->registry->getManager();
		/** @var AuthCodeEntity|null $authCodeEntity */
		$authCodeEntity = $manager->getRepository(AuthCodeEntity::class)->fetchOne($this->createQuery()->byIdentifier($codeId));
		if ($authCodeEntity !== null) {
			$authCodeEntity->setRevoked(true);
			$manager->flush();
		}
	}

	/**
	 * @param string $codeId
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function isAuthCodeRevoked($codeId): bool
	{
		/** @var AuthCodeEntity|null $authCodeEntity */
		$authCodeEntity = $this->registry->getManager()->getRepository(AuthCodeEntity::class)->fetchOne($this->createQuery()->byIdentifier($codeId));
		return $authCodeEntity !== null ? $authCodeEntity->isRevoked() : true;
	}

	protected function createQuery(): AuthCodeQuery
	{
		return new AuthCodeQuery();
	}

}
