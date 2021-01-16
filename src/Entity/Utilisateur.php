<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 */
class Utilisateur
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $motDePasse;

    /**
     * @ORM\Column(type="date")
     */
    private $dateNaissance;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $telephone;

    /**
     * @ORM\Column(type="date")
     */
    private $dateEmbauche;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estCadre;

    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(string $nom): self {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self {
        $this->prenom = $prenom;
        return $this;
    }

    public function getMotDePasse(): ?string {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): self {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }

    public function getTelephone(): ?string {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self {
        $this->telephone = $telephone;
        return $this;
    }

    public function getDateEmbauche(): ?\DateTimeInterface {
        return $this->dateEmbauche;
    }

    public function setDateEmbauche(\DateTimeInterface $dateEmbauche): self {
        $this->dateEmbauche = $dateEmbauche;
        return $this;
    }

    public function getEstCadre(): ?bool {
        return $this->estCadre;
    }

    public function setEstCadre(bool $estCadre): self {
        $this->estCadre = $estCadre;
        return $this;
    }
}
