<?php

namespace App\Controller\Security;

use App\Form\Security\ChangeUserPasswordFormType;
use App\Repository\Property\PropertyRepository;
use App\Repository\Security\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

        return $this->render('security/changePassword/change_password.html.twig', [
            'changeUserPassword' => $form->createView(),
        ]);
    }

    #[Route('/deleteAccount', name: 'deleteAccount')]
    public function deleteAccount(Request $request, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $ownerId = $request->getSession()->get('ownerId');
        $user = $userRepository->fetchAllData($ownerId);
        foreach ($user as $value) {
            $em->remove($value);
        }
        $em->flush();

        return $this->redirectToRoute('login');
    }


}
