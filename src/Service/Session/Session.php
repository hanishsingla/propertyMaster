<?php

namespace App\Service\Session;

class Session
{
    public function session($user, $request): void
    {
        $request->getSession()->set('email'    , $user->getEmail());
        $request->getSession()->set('ownerId'  , $user->getId());
        $request->getSession()->set('gender'  , $user->getUserAddress()->getGender());
        $request->getSession()->set('userImage'  , $user->getUserAddress()->getImage());
        $request->getSession()->set('userName' , $user->getUserAddress()->getName());
    }
}