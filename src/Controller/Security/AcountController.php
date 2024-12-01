<?php

namespace App\Controller\Security;

use App\Form\Security\ChangeUserPasswordFormType;
use App\Form\Security\UserDetailType;
use App\Repository\Security\UserDetailRepository;
use App\Repository\Security\UserRepository;
use App\Service\CommonHelper;
use App\Service\FileUploader\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AcountController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/account', name: 'account')]
    public function account(Request $request, UserDetailRepository $detailRepository, Uploader $uploader, EntityManagerInterface $em): Response
    {
        $ownerId = $request->getSession()->get('ownerId');
        $userDetail = $detailRepository->getUser($ownerId);
        $userImage = $userDetail->getImage();
        $form = $this->createForm(UserDetailType::class, $userDetail);
        $form->handleRequest($request);

        /** @var UploadedFile $image */
        $image = $form->get('image')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($image) {
                $imageName = $uploader->upload($image, CommonHelper::USER_IMAGE_UPLOAD);
                $userDetail->setImage($imageName);
            }
            $em->persist($userDetail);
            $em->flush();

            return $this->redirectToRoute('account');
        }

        return $this->render('base/user_profile.html.twig', [
            'form' => $form->createView(),
            'userImage' => $userImage,
        ]);
    }

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
