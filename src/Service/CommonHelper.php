<?php

namespace App\Service;

use DateTime;

class CommonHelper
{
    public function setCreatedDate($information): void
    {
        $date = new DateTime;
        $information->setIsCreatedAt($date);
    }
}