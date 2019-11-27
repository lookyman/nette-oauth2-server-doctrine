<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\AuthCode;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="auth_code")
 */
class AuthCodeEntity implements AuthCodeEntityInterface
{

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
	 * @ORM\Column(type="text")
	 * @var string
	 */
	private $redirectUri;

	/**
	 * @ORM\Column(type="string", length=80, unique=true)
	 * @var string
	 */
	private $identifier;

	/**
	 * @ORM\Column(type="datetimetz")
	 * @var \DateTime
	 */
	private $expiryDateTime;

	/**
	 * @ORM\Column(type="text")
	 * @var string
	 */
	private $userIdentifier;

	/**
	 * @ORM\ManyToOne(targetEntity="Lookyman\NetteOAuth2Server\Storage\Doctrine\Client\ClientEntity")
	 * @ORM\JoinColumn(nullable=false)
	 * @var ClientEntity
	 */
	private $client;

	/**
	 * @ORM\ManyToMany(targetEntity="Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope\ScopeEntity")
	 * @ORM\JoinTable(name="auth_code_scope")
	 * @var Collection of ScopeEntity
	 */
	private $scopes;

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

	public function getRedirectUri(): string
	{
		return $this->redirectUri;
	}

	/**
	 * @param string $uri
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function setRedirectUri($uri): void
	{
		$this->redirectUri = (string) $uri;
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

	public function getExpiryDateTime(): \DateTime
	{
		return $this->expiryDateTime;
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

	public function getUserIdentifier()
	{
		return $this->userIdentifier;
	}

	public function getClient(): ClientEntityInterface
	{
		return $this->client;
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

	/**
	 * @return ScopeEntityInterface[]
	 */
	public function getScopes(): array
	{
		return $this->scopes->toArray();
	}

}
