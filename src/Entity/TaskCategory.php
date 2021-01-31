<?php

namespace App\Entity;

use App\Repository\TaskCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskCategoryRepository::class)
 */
class TaskCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRemote;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPhysical;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIsRemote(): ?bool
    {
        return $this->isRemote;
    }

    public function setIsRemote(bool $isRemote): self
    {
        $this->isRemote = $isRemote;

        return $this;
    }

    public function getIsPhysical(): ?bool
    {
        return $this->isPhysical;
    }

    public function setIsPhysical(bool $isPhysical): self
    {
        $this->isPhysical = $isPhysical;

        return $this;
    }
}
