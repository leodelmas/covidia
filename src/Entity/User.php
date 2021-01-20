<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface,\Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=100)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=100)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="date")
     */
    private $birthDate;

    /**
     * @ORM\Column(type="date")
     */
    private $hiringDate;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/[0-9]{10}/"
     * )
     */
    private $phone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isExecutive;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAdmin;

    /**
     * @ORM\OneToMany(targetEntity=WorkingDay::class, mappedBy="user", orphanRemoval=true)
     */
    private $workingDays;

    public function __construct()
    {
        $this->workingDays = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self {
        $this->lastname = $lastname;
        return $this;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): self {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function getHiringDate(): ?\DateTimeInterface {
        return $this->hiringDate;
    }

    public function setHiringDate(\DateTimeInterface $hiringDate): self {
        $this->hiringDate = $hiringDate;
        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string {
        return $this->phone;
    }

    public function setPhone(string $phone): self {
        $this->phone = $phone;
        return $this;
    }

    public function getIsExecutive(): ?bool {
        return $this->isExecutive;
    }

    public function setIsExecutive(bool $isExecutive): self {
        $this->isExecutive = $isExecutive;
        return $this;
    }

    public function getIsAdmin(): ?bool {
        return $this->isAdmin;
    }

    public function setIsAdmin($isAdmin): self {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function serialize(): ?string {
        return serialize([
            $this->id,
            $this->email,
            $this->password
        ]);
    }

    public function unserialize($serialized) {
        list (
            $this ->id,
            $this ->email,
            $this ->password
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function getSalt() {
        return null;
    }

    public function getUsername(): string {
        return $this->email;
    }

    public function eraseCredentials(){
    }

    public function getRoles() {
        if($this->isAdmin) {
            $roles[] = 'ROLE_ADMIN';
        }
        else {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    /**
     * @return Collection|WorkingDay[]
     */
    public function getWorkingDays(): Collection
    {
        return $this->workingDays;
    }

    public function addWorkingDay(WorkingDay $workingDay): self
    {
        if (!$this->workingDays->contains($workingDay)) {
            $this->workingDays[] = $workingDay;
            $workingDay->setUser($this);
        }

        return $this;
    }

    public function removeWorkingDay(WorkingDay $workingDay): self
    {
        if ($this->workingDays->removeElement($workingDay)) {
            // set the owning side to null (unless already changed)
            if ($workingDay->getUser() === $this) {
                $workingDay->setUser(null);
            }
        }

        return $this;
    }
}
