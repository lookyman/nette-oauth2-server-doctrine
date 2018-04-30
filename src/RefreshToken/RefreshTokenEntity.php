<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\RefreshToken;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use Lookyman\NetteOAuth2Server\Storage\Doctrine\AccessToken\AccessTokenEntity;

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
	 * @var int|null
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
	 * @var AccessTokenEntity
	 */
	private $accessToken;

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
	 * @param AccessTokenEntity $accessToken
	 */
	public function setAccessToken(AccessTokenEntityInterface $accessToken): void
	{
		$this->accessToken = $accessToken;
	}

	public function getAccessToken(): AccessTokenEntityInterface
	{
		return $this->accessToken;
	}

}
