<?php

namespace App\Entity;

use App\Repository\WorkTimeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Security;

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
     */
    private $dateStart;

    /**
     * @ORM\Column(type="date")
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

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setWorkTime($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getWorkTime() === $this) {
                $task->setWorkTime(null);
            }
        }

        return $this;
    }
}
