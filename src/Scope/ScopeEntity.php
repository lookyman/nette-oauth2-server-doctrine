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
	 * @var int|null
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=80, unique=true)
	 * @var string
	 */
	private $identifier;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function __clone()
	{
		$this->id = null;
	}

	public function getIdentifier(): string
	{
		return $this->identifier;
	}

	public function setIdentifier(string $identifier): void
	{
		$this->identifier = $identifier;
	}

	public function jsonSerialize(): string
	{
		return $this->getIdentifier();
	}

}
