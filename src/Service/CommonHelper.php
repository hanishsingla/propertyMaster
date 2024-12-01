<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CommonHelper
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public const LOGIN_ROUTE = 'login';

    public const ROLE_AGENT = 'ROLE_AGENT';

    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const HOME_ROUTE = 'home';
    public const ADMIN_ROUTE = 'admin';
    public const AGENT_ROUTE = 'userProperty';
    public const Property_IMAGE_UPLOAD = '/uploads/propertyImages';
    public const USER_IMAGE_UPLOAD = '/uploads/userImages';

    public function redirect($user): RedirectResponse
    {
        if (!$user) {
            $url = $this->urlGenerator->generate(self::LOGIN_ROUTE);
        } elseif (in_array(self::ROLE_AGENT, $user->getRoles())) {
            $url = $this->urlGenerator->generate(self::AGENT_ROUTE);
        } elseif (in_array(self::ROLE_ADMIN, $user->getRoles())) {
            $url = $this->urlGenerator->generate(self::ADMIN_ROUTE);
        } else {
            $url = $this->urlGenerator->generate(self::HOME_ROUTE);
        }

        return new RedirectResponse($url);
    }
}
