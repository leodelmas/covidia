<?php

namespace App\Entity;

use App\Repository\WorkTimeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=WorkTimeRepository::class)
 */
class WorkTime
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Assert\Expression("value <= this.getDateEnd()")
     */
    private $dateStart;

    /**
     * @ORM\Column(type="date")
     * @Assert\Expression("value >= this.getDateStart()")
     */
    private $dateEnd;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isTeleworked;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="workTimes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="workTime", orphanRemoval=true)
     */
    private $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getDateStart(): ?\DateTimeInterface {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self {
        $this->dateStart = $dateStart;
        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): self {
        $this->dateEnd = $dateEnd;
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

    public function getTasks(): Collection {
        return $this->tasks;
    }

    public function addTask(Task $task): self {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setWorkTime($this);
        }
        return $this;
    }

    public function removeTask(Task $task): self {
        if ($this->tasks->removeElement($task)) {
            if ($task->getWorkTime() === $this) {
                $task->setWorkTime(null);
            }
        }
        return $this;
    }

    public function displayLabel(): string {
        $teleworked = $this->getIsTeleworked() ? " (Télétravail)" : " (Présentiel)";
        return $this->getDateStart()->format("d/m/Y") . " > " . $this->getDateEnd()->format("d/m/Y") . $teleworked;
    }
}
