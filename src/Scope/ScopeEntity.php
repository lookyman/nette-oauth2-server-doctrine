<?php

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="nette_oauth2_server_scope")
 */
class ScopeEntity implements ScopeEntityInterface
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=80, unique=true)
	 * @var string
	 */
	private $identifier;

	public function __clone()
	{
		$this->id = null;
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
	public function jsonSerialize()
	{
		return $this->getIdentifier();
	}
}
