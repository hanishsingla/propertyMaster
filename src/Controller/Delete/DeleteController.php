<?php

namespace App\Controller\Delete;

use App\Repository\Property\PropertyRepository;
use App\Repository\Security\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteController extends  AbstractController
{
    #[Route('/deleteAccount', name: 'deleteAccount')]
    public function deleteAccount(Request $request ,UserRepository $userRepository ,EntityManagerInterface $em) : Response
    {
        $ownerId = $request->getSession()->get('ownerId');
        $user = $userRepository->fetchAllData($ownerId);
        foreach ($user as $value) {
            $em->remove($value);
        }
        $em->flush();
        return $this->redirectToRoute('login');
    }

    #[IsGranted('ROLE_AGENT')]
    #[Route('/propertyDelete/{id}', name: 'propertyDelete')]
    public function delete(Request $request, EntityManagerInterface $em, PropertyRepository $propertyRepository, $id = null): Response
    {
        $ownerId = $request->getSession()->get('ownerId');

        $propertyData = $propertyRepository->findOneBy(['id' => $id, 'ownerId' => $ownerId]);

        $em->remove($propertyData);

        $em->flush();

        return $this->redirectToRoute('userProperty');
    }
}