<?php

namespace App\Service\Helper;

use App\Service\CommonHelper;

class PropertyHelper
{
    public function __construct(private readonly  CommonHelper $commonHelper)
    {

    }
    public function setPropertyInformation($form, $ownerId): void
    {
        $commonHelper= $this->commonHelper;
        $information = $form->getData();
        $information->setOwnerId($ownerId);

        $commonHelper->setCreatedDate($information);
    }
}