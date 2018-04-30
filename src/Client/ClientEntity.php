<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Client;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\ClientEntityInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="client")
 */
class ClientEntity implements ClientEntityInterface
{

	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 * @var int|null
	 */
	private $id;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @var string|null
	 */
	private $secret;

	/**
	 * @ORM\Column(type="string", length=80, unique=true)
	 * @var string
	 */
	private $identifier;

	/**
	 * @ORM\Column(type="text")
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\Column(type="text")
	 * @var string
	 */
	private $redirectUri;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function __clone()
	{
		$this->id = null;
	}

	public function getSecret(): ?string
	{
		return $this->secret;
	}

	/**
	 * @param string|null $secret
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function setSecret($secret): void
	{
		$this->secret = $secret;
	}

	public function getIdentifier(): string
	{
		return $this->identifier;
	}

	public function setIdentifier(string $identifier): void
	{
		$this->identifier = $identifier;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getRedirectUri(): string
	{
		return $this->redirectUri;
	}

	public function setRedirectUri(string $uri): void
	{
		$this->redirectUri = $uri;
	}

}
