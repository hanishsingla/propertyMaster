<?php

namespace App\Controller\Security;


use App\Entity\ResetPassword;
use App\Form\ChangeUserPasswordFormType;
use App\Repository\UserRepository;
use Exception;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChangePasswordController extends AbstractController
{
    #[Route('/changeUserPassword', name: 'app_change_password')]
    public function changePassword(Request $request, UserRepository $userRepository, $companyUserId): Response
    {

        $user = $userRepository->loadWebUserByOauthUid($companyUserId);

        $form = $this->createForm(ChangeUserPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        }

    return $this->render('security/changePassword/change_password.html.twig',[
        'changeUserPassword' => $form->createView(),
    ]);

    }
}