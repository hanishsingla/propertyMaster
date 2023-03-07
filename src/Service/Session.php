<?php

namespace App\Service;

class Session
{
    public function session($user, $request): void
    {
        $request->getSession()->set('email'    , $user->getEmail());
        $request->getSession()->set('ownerId'  , $user->getId());
        $request->getSession()->set('userName' , $user->getUserName());
    }
}