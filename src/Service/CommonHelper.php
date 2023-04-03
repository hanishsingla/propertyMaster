<?php

namespace App\Service;

use DateTime;

class CommonHelper
{
    public function setRegisterUser($data) : void
    {
        $userInformation = $data->getUserAddress();
        $this->setCreatedDate($data);
        $this->setCreatedDate($userInformation);
    }

    public function setPropertyInformation($form, $ownerId): void
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

    public function setUpdateDate($form): void
    {
        $information = $form->getData();
        $date = new DateTime;
        $information->setIsUpdatedAt($date);
    }
}