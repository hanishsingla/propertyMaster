<?php

namespace App\Service\Helper;

use App\Service\CommonHelper;
use Symfony\Component\Uid\Uuid;

class RegisterHelper
{
    public function __construct(private readonly  CommonHelper $commonHelper)
    {

    }
  public function setRegisterUser($data) : void
   {
       $commonHelper= $this->commonHelper;

       $uuid = Uuid::v4();
       $data->setOwnerId($uuid);
       $userInformation = $data->getUserInformation();
       $userInformation->setOwnerId($data->getOwnerId());
       $commonHelper->setCreatedDate($data);
       $commonHelper->setCreatedDate($userInformation);
   }
}