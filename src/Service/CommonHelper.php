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
    public function setUpdateDate($form): void
    {
        $information = $form->getData();
        $date = new DateTime;
        $information->setIsUpdatedAt($date);
    }
}