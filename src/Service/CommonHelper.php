<?php

namespace App\Service;

use DateTime;

class CommonHelper
{

    public function setInformation($form, $ownerId): void
    {
        $information = $form->getData();
        $information->setOwnerId($ownerId);
        $this->setCreatedDate($information);
    }

    public function setCreatedDate($information): void
    {
        $date = new DateTime;
        $information->setIsCreatedAt($date);

    }

}