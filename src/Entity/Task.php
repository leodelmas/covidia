<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Expression("value < this.getDateTimeEnd()")
     */
    private $dateTimeStart;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Expression("value > this.getDateTimeStart()")
     */
    private $dateTimeEnd;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=10, max=255)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=WorkTime::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $workTime;

    /**
     * @ORM\ManyToOne(targetEntity=TaskCategory::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $taskCategory;

    public function getId(): ?int {
        return $this->id;
    }

    public function getDateTimeStart(): ?\DateTimeInterface {
        return $this->dateTimeStart;
    }

    public function setDateTimeStart(\DateTimeInterface $dateTimeStart): self {
        $this->dateTimeStart = $dateTimeStart;
        return $this;
    }

    public function getDateTimeEnd(): ?\DateTimeInterface {
        return $this->dateTimeEnd;
    }

    public function setDateTimeEnd(\DateTimeInterface $dateTimeEnd): self {
        $this->dateTimeEnd = $dateTimeEnd;
        return $this;
    }

    public function getComment(): ?string {
        return $this->comment;
    }

    public function setComment(string $comment): self {
        $this->comment = $comment;
        return $this;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): self {
        $this->user = $user;
        return $this;
    }

    public function getWorkTime(): ?WorkTime {
        return $this->workTime;
    }

    public function setWorkTime(?WorkTime $workTime): self {
        $this->workTime = $workTime;
        return $this;
    }

    public function getTaskCategory(): ?TaskCategory
    {
        return $this->taskCategory;
    }

    public function setTaskCategory(?TaskCategory $taskCategory): self
    {
        $this->taskCategory = $taskCategory;

        return $this;
    }
}
