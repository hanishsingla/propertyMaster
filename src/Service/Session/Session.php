<?php

namespace App\Service\Session;

class Session
{
    public function session($user, $request): void
    {
        $request->getSession()->set('email'    , $user->getEmail());
        $request->getSession()->set('ownerId'  , $user->getOwnerId());
        $request->getSession()->set('gender'  , $user->getUserinformation()->getGender());
        $request->getSession()->set('userName' , $user->getUserName());
    }
}