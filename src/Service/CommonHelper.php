<?php

namespace App\Service;

use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CommonHelper
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    )
    {

    }

    public const  LOGIN_ROUTE = "login";

    public const  ROLE_AGENT = "ROLE_AGENT";

    public const  ROLE_ADMIN = "ROLE_ADMIN";
    public const  HOME_ROUTE = "home";
    public const  ADMIN_ROUTE = "admin";
    public const  AGENT_ROUTE = "userProperty";
    public const Property_IMAGE_UPLOAD = '/uploads/propertyImages';
    public const USER_IMAGE_UPLOAD = '/uploads/userImages';

    public function setRegisterUser($data) : void
    {
        if($data->isAgent()){
            $data->setRoles([self::ROLE_AGENT]);
        }
        $this->setCreatedDate($data);
        $userDetail = $data->getUserDetail();
        $this->setCreatedDate($userDetail);
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


    public function redirect($user): RedirectResponse
    {
        if (!$user) {
            $url = $this->urlGenerator->generate(self::LOGIN_ROUTE);
        } else {
            if (array_intersect($user->getRoles(), (array) self::ROLE_AGENT)) {
                $url = $this->urlGenerator->generate(self::AGENT_ROUTE);
            } elseif (array_intersect($user->getRoles(), (array) self::ROLE_ADMIN)) {
                $url = $this->urlGenerator->generate(self::ADMIN_ROUTE);
            } else {
                $url = $this->urlGenerator->generate(self::HOME_ROUTE);
            }

        }

        return new RedirectResponse($url);
    }
}