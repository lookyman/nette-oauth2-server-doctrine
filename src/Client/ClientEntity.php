<?php

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Client;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\ClientEntityInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="nette_oauth2_server_client")
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
	 * @return string
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getRedirectUri()
	{
		return $this->redirectUri;
	}
}