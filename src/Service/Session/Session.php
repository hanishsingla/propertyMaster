<?php

namespace App\Service\Session;

class Session
{
    public function session($user, $request): void
    {
        $request->getSession()->set('email'    , $user->getEmail());
        $request->getSession()->set('ownerId'  , $user->getId());
        $request->getSession()->set('gender'  , $user->getGender());
        $request->getSession()->set('userImage'  , $user->getImage());
        $request->getSession()->set('userName' , $user->getName());
    }
}