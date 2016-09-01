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
	 * @var int
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
	 * @return string|null
	 */
	public function getSecret()
	{
		return $this->secret;
	}

	/**
	 * @param string|null $secret
	 */
	public function setSecret($secret)
	{
		$this->secret = $secret;
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
	public function setIdentifier(string $identifier)
	{
		$this->identifier = $identifier;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name)
	{
		$this->name = $name;
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
	public function setRedirectUri(string $uri)
	{
		$this->redirectUri = $uri;
	}
}
