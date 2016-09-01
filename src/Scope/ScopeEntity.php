<?php
declare(strict_types=1);

namespace Lookyman\NetteOAuth2Server\Storage\Doctrine\Scope;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="scope")
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
	public function jsonSerialize()
	{
		return $this->getIdentifier();
	}
}
