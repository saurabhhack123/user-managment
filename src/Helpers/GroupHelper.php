<?php

namespace App\Helpers;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class GroupHelper
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param array $ids
     * @param Group $group
     * @param string $type
     */
    public function manageGroupUsers(array $ids, Group $group, string $type)
    {
        $users = $this->em->getRepository(User::class)->findBy(['id' => $ids]);

        foreach ($users as $user) {
            'add' === $type ? $user->addGroup($group): $user->removeGroup($group);
        }
    }

    /**
     * @param Group|null $group
     * @param array      $ids
     *
     * @return array
     */
    public function validate(Group $group = null, array $ids)
    {
        if (!$group) {
            return [
                'isValid' => false,
                'message' => 'Group does not exist.',
            ];
        }

        if (0 === count($ids)) {
            return [
                'isValid' => false,
                'message' => 'Please provide users.',
            ];
        }

        return ['isValid' => true];
    }


    public function areUserExists(Group $group)
    {
  
        if (count($group->getUsers())>0){
            return [
                'isValid' => false
            ];
        }

        return [
            'isValid' => True
        ];
    }
}
