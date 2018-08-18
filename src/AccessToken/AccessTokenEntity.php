<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="access_token")
 */
class AccessTokenEntity implements AccessTokenEntityInterface
{

	use AccessTokenTrait;

	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 * @var int|null
	 */
	private $id;

	/**
	 * @ORM\Column(type="boolean")
	 * @var bool
	 */
	private $revoked = false;

	/**
	 * @ORM\ManyToOne(targetEntity="Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity", cascade={"persist"})
	 * @ORM\JoinColumn(nullable=false)
	 * @var ClientEntity
	 */
	private $client;

	/**
	 * @ORM\Column(type="datetimetz")
	 * @var \DateTime
	 */
	private $expiryDateTime;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @var string
	 */
	private $userIdentifier;

	/**
	 * @ORM\ManyToMany(targetEntity="Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity")
	 * @ORM\JoinTable(name="access_token_scope")
	 * @var Collection of ScopeEntity
	 */
	private $scopes;

	/**
	 * @ORM\Column(type="string", length=80, unique=true)
	 * @var string
	 */
	private $identifier;

	public function __construct()
	{
		$this->scopes = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function __clone()
	{
		$this->id = null;
	}

	public function isRevoked(): bool
	{
		return $this->revoked;
	}

	public function setRevoked(bool $revoked): void
	{
		$this->revoked = $revoked;
	}

	public function getClient(): ClientEntityInterface
	{
		return $this->client;
	}

	public function getExpiryDateTime(): \DateTime
	{
		return $this->expiryDateTime;
	}

	public function getUserIdentifier(): ?string
	{
		return $this->userIdentifier;
	}

	/**
	 * @return ScopeEntityInterface[]
	 */
	public function getScopes(): array
	{
		return $this->scopes->toArray();
	}

	public function getIdentifier(): string
	{
		return $this->identifier;
	}

	/**
	 * @param string $identifier
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function setIdentifier($identifier): void
	{
		$this->identifier = $identifier;
	}

	public function setExpiryDateTime(\DateTime $dateTime): void
	{
		$this->expiryDateTime = $dateTime;
	}

	/**
	 * @param string $identifier
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function setUserIdentifier($identifier): void
	{
		$this->userIdentifier = $identifier;
	}

	public function setClient(ClientEntityInterface $client): void
	{
		if ($client instanceof ClientEntity) {
			$this->client = $client;
		}
	}

	public function addScope(ScopeEntityInterface $scope): void
	{
		if ($scope instanceof ScopeEntity && !$this->scopes->contains($scope)) {
			$this->scopes->add($scope);
		}
	}

}
