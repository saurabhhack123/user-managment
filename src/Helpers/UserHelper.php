<?php

namespace App\Helpers;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

class UserHelper
{
    const paths = ['name', 'email', 'password', 'roles'];

    /**
     * @param User    $user
     * @param Request $request
     *
     * @throws \TypeError
     *
     * @return User
     */
    public function createUser(User $user, Request $request)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach (self::paths as $path) {
            $propertyAccessor->setValue($user, $path, $request->get($path, null));
        }

        return $user;
    }

    /**
     * @param User    $user
     * @param Request $request
     *
     * @throws \TypeError
     *
     * @return User
     */
    public function editUser(User $user, Request $request)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach (self::paths as $path) {
            $propertyAccessor->setValue(
                $user,
                $path,
                $request->get($path, $propertyAccessor->getValue($user, $path))
            );
        }

        return $user;
    }
}
