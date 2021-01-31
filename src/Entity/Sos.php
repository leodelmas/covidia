<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Sos
{
    const TYPE_EMAIL = [
        0 => "Email1",
        1 => "Email2"
    ];

    /**
     * @var string
     * @Assert\Length(min=5, max=50)
     */
    private $sujet;

    /**
     * @var integer
     */
    private $email;

    /**
     * @var User
     */
    private $user;

    /**
     * @return string
     */
    public function getSujet(): string
    {
        return $this->sujet;
    }

    /**
     * @param string $sujet
     * @return Sos
     */
    public function setSujet(string $sujet): Sos
    {
        $this->sujet = $sujet;
        return $this;
    }

    /**
     * @return integer
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param integer $email
     * @return Sos
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmailType(): string
    {
        return self::TYPE_EMAIL[$this->email];
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Sos
     */
    public function setUser(User $user): Sos
    {
        $this->user = $user;
        return $this;
    }
}
