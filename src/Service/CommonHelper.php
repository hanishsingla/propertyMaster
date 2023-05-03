<?php

namespace App\Service;

use DateTime;

class CommonHelper
{
    public function setRegisterUser($data) : void
    {
        if($data->isAgent()){
            $data->setRoles(['ROLE_AGENT']);
        }

        $this->setCreatedDate($data);
        $userInformation = $data->getUserAddress();
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

    public function setUpdateDate($information): void
    {
        $date = new DateTime;
        $information->setIsUpdatedAt($date);
    }
}