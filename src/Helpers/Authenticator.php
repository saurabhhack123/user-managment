<?php

namespace App\Helpers;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class Authenticator
{
    /** @var EntityManagerInterface */
    public $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string|null $token
     *
     * @return bool
     */
    public function isAuthenticated(string $token)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['token' => $token]);

        if (!$user) {
            return false;
        }

        if (!$user->hasRole('ROLE_ADMIN')) {
            return false;
        }

        return true;
    }
}
