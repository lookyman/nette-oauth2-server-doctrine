<?php

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
	 * @var int
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

	public function __clone()
	{
		$this->id = null;
	}

	/**
	 * @return bool
	 */
	public function isRevoked()
	{
		return $this->revoked;
	}

	/**
	 * @param bool $revoked
	 */
	public function setRevoked($revoked)
	{
		$this->revoked = $revoked;
	}

	/**
	 * @return ClientEntityInterface
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * @return \DateTime
	 */
	public function getExpiryDateTime()
	{
		return $this->expiryDateTime;
	}

	/**
	 * @return string
	 */
	public function getUserIdentifier()
	{
		return $this->userIdentifier;
	}

	/**
	 * @return ScopeEntityInterface[]
	 */
	public function getScopes()
	{
		return $this->scopes->toArray();
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
	 * @param ClientEntityInterface $client
	 */
	public function setClient(ClientEntityInterface $client)
	{
		$this->client = $client;
	}

	/**
	 * @param ScopeEntityInterface $scope
	 */
	public function addScope(ScopeEntityInterface $scope)
	{
		if (!$this->scopes->contains($scope)) {
			$this->scopes->add($scope);
		}
	}
}
