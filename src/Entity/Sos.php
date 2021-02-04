<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Sos {

    /**
     * @var string
     * @Assert\Length(min=5, max=50)
     */
    private $message;

    /**
     * @var User
     */
    private $user;

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
}
