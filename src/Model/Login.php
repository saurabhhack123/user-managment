<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Login
{

    /**
         * @var string
         *
         * @Assert\NotBlank(message="Please provide email.")
         *
         */   
    private $email;
    /**
         * @var string
         *
         * @Assert\NotBlank(message="Please provide password.")
         *
         */   
    private $password;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }



}



?>