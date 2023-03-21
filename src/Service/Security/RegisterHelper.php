<?php

namespace App\Service\Security;

use DateTime;

class RegisterHelper
{
    public function setCreatedDate($information): void
    {
        $date = new DateTime;
        $information->setIsCreatedAt($date);

    }

}