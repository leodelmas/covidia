<?php

namespace App\Entity;

use App\Repository\WorkingDayRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WorkingDayRepository::class)
 */
class WorkingDay
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isTeleworked;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="workingDays")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self {
        $this->date = $date;
        return $this;
    }

    public function getIsTeleworked(): ?bool {
        return $this->isTeleworked;
    }

    public function setIsTeleworked(bool $isTeleworked): self {
        $this->isTeleworked = $isTeleworked;
        return $this;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): self {
        $this->user = $user;
        return $this;
    }
}
