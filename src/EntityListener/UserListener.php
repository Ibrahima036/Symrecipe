<?php

namespace App\EntityListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class UserListener
{
    private UserPasswordHasherInterface $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function prePersit(User $user)
    {
        $this->encodePasswordd($user);
    }
    public function preUpdate(User $user)
    {
        $this->encodePasswordd($user);
    }
    /**
     * encoding password
     *
     * @param User $user
     * @return void
     */
    public function encodePasswordd(User $user)
    {
        if ($user->getPainPassword() === null) {
            return;
        }
        $user->setPassword(
            $this->hasher->hashPassword(
                $user,
                $user->getPainPassword()
            )
        );
        $user->setPainPassword(null);
    }
}
