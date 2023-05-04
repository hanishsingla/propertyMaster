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

    public function setRegisterUser($data) : void
    {
        if($data->isAgent()){
            $data->setRoles([self::ROLE_AGENT]);
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


    public function redirect($user): RedirectResponse
    {
        if (!$user) {
            $url = $this->urlGenerator->generate(self::LOGIN_ROUTE);
        } else {
            if ($user->getRoles()[0] == self::ROLE_AGENT) {
                $url = $this->urlGenerator->generate(self::AGENT_ROUTE);
            } elseif ($user->getRoles()[0] == self::ROLE_ADMIN) {
                $url = $this->urlGenerator->generate(self::ADMIN_ROUTE);
            } else {
                $url = $this->urlGenerator->generate(self::HOME_ROUTE);
            }
        }

        return new RedirectResponse($url);
    }
}