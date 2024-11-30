<?php

namespace App\Service\Session;

class Session
{
    public function session($user, $request): void
    {
        $request->getSession()->set('email', $user->getEmail());
        $request->getSession()->set('ownerId', $user->getId());
        $request->getSession()->set('gender', $user->getUserDetail()->getGender());
        $request->getSession()->set('userImage', $user->getUserDetail()->getImage());
        $request->getSession()->set('userName', $user->getUserDetail()->getName());
    }
}
