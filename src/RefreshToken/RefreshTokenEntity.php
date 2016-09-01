<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="refresh_token")
 */
class RefreshTokenEntity implements RefreshTokenEntityInterface
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
	 * @ORM\ManyToOne(targetEntity="Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenEntity")
	 * @var AccessToken;
	 */
	private $accessToken;

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
	 * @param AccessTokenEntityInterface $accessToken
	 */
	public function setAccessToken(AccessTokenEntityInterface $accessToken)
	{
		$this->accessToken = $accessToken;
	}

	/**
	 * @return AccessTokenEntityInterface
	 */
	public function getAccessToken()
	{
		return $this->accessToken;
	}
}
