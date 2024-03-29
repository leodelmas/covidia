<?php

namespace App\Entity;

use Exception;
use App\Entity\Job;
use App\Entity\Task;
use App\Entity\WorkTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface; 
/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Vich\Uploadable()
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
     * @var File|null
     * @Assert\Image(
     *      mimeTypes="image/jpeg"
     * )
     * @Vich\UploadableField(mapping="user_image", fileNameProperty="fileName")
     */
    private $imageFile;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;


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
    private $isPsychologist;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAdmin;

    /**
     * @ORM\ManyToOne(targetEntity=Job::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $job;

    /**
     * @ORM\OneToMany(targetEntity=WorkTime::class, mappedBy="user", orphanRemoval=true)
     */
    private $workTimes;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="user", orphanRemoval=true)
     */
    private $tasks;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $file_name;

    public function __construct()
    {
        $this->workTimes = new ArrayCollection();
        $this->tasks = new ArrayCollection();
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

    public function getIsPsychologist(): ?bool {
        return $this->isPsychologist;
    }

    public function setIsPsychologist(bool $isPsychologist): self {
        $this->isPsychologist = $isPsychologist;
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

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

        return $this;
    }



    
    public function getFileName(): ?string
    {
        return $this->file_name;
    }

    
    public function setFileName(?string $file_name): User
    {
        $this->file_name = $file_name;
        return $this;
    }

    /**
     *
     * @param null|File $imageFile
     * @return Property
     */
    public function setImageFile(?File $imageFile = null): User
    {
        $this->imageFile = $imageFile;
        if (null !== $imageFile) {
            $this->updated_at = new \DateTimeImmutable();
        }
        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @return Collection|WorkTime[]
     */
    public function getWorkTimes(): Collection
    {
        return $this->workTimes;
    }

    public function addWorkTime(WorkTime $workTime): self
    {
        if (!$this->workTimes->contains($workTime)) {
            $this->workTimes[] = $workTime;
            $workTime->setUser($this);
        }

        return $this;
    }

    public function removeWorkTime(WorkTime $workTime): self
    {
        if ($this->workTimes->removeElement($workTime)) {
            // set the owning side to null (unless already changed)
            if ($workTime->getUser() === $this) {
                $workTime->setUser(null);
            }
        }

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
            $task->setUser($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getUser() === $this) {
                $task->setUser(null);
            }
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
    
}
