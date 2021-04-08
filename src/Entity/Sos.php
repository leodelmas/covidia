<?php

namespace App\Entity;

use App\Repository\SosRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=SosRepository::class)
 */
class Sos {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=20, max=2000)
     * @Assert\NotBlank()
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAnonymous;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isClosed;

    public function __construct()
    {
        $this->isClosed = false;
    }

    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMessage(): string {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Sos
     */
    public function setMessage(string $message): Sos {
        $this->message = $message;
        return $this;
    }
    
    /**
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Sos
     */
    public function setUser(User $user): Sos {
        $this->user = $user;
        return $this;
    }

    public function getIsAnonymous(): ?bool {
        return $this->isAnonymous;
    }

    public function setIsAnonymous(bool $isAnonymous): self {
        $this->isAnonymous = $isAnonymous;
        return $this;
    }

    public function getIsClosed(): ?bool {
        return $this->isClosed;
    }

    public function setIsClosed(bool $isClosed): self {
        $this->isClosed = $isClosed;
        return $this;
    }
}
