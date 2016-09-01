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
	 * @var int
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

	/**
	 * @return int|null
	 */
	public function getId()
	{
		return $this->id;
	}

	public function __clone()
	{
		$this->id = null;
	}

	/**
	 * @return bool
	 */
	public function isRevoked(): bool
	{
		return $this->revoked;
	}

	/**
	 * @param bool $revoked
	 */
	public function setRevoked(bool $revoked)
	{
		$this->revoked = $revoked;
	}

	/**
	 * @return string
	 */
	public function getRedirectUri()
	{
		return $this->redirectUri;
	}

	/**
	 * @param string $uri
	 */
	public function setRedirectUri($uri)
	{
		$this->redirectUri = (string) $uri;
	}

	/**
	 * @return string
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * @param string $identifier
	 */
	public function setIdentifier($identifier)
	{
		$this->identifier = $identifier;
	}

	/**
	 * @return \DateTime
	 */
	public function getExpiryDateTime()
	{
		return $this->expiryDateTime;
	}

	/**
	 * @param \DateTime $dateTime
	 */
	public function setExpiryDateTime(\DateTime $dateTime)
	{
		$this->expiryDateTime = $dateTime;
	}

	/**
	 * @param string $identifier
	 */
	public function setUserIdentifier($identifier)
	{
		$this->userIdentifier = $identifier;
	}

	/**
	 * @return string
	 */
	public function getUserIdentifier()
	{
		return $this->userIdentifier;
	}

	/**
	 * @return ClientEntityInterface
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * @param ClientEntityInterface $client
	 */
	public function setClient(ClientEntityInterface $client)
	{
		if ($client instanceof ClientEntity) {
			$this->client = $client;
		}
	}

	/**
	 * @param ScopeEntityInterface $scope
	 */
	public function addScope(ScopeEntityInterface $scope)
	{
		if ($scope instanceof ScopeEntity && !$this->scopes->contains($scope)) {
			$this->scopes->add($scope);
		}
	}

	/**
	 * @return ScopeEntityInterface[]
	 */
	public function getScopes()
	{
		return $this->scopes->toArray();
	}
}
