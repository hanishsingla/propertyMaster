<?php

namespace App\Controller\Property;

use App\Entity\Property\Property;
use App\Form\Property\PropertyType;
use App\Repository\Property\PropertyRepository;
use App\Service\CommonHelper;
use App\Service\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserPropertyController extends AbstractController
{
    #[IsGranted('ROLE_AGENT')]
    #[Route('/userProperty', name: 'userProperty')]
    public function userProperty(Request $request, PropertyRepository $propertyRepository): Response
    {
        $ownerId = $request->getSession()->get('ownerId');

        $propertyLists = $propertyRepository->getOwnerProperties($ownerId);

        return $this->render('userProperty/user_property.html.twig', [
            'propertyLists' => $propertyLists,
        ]);
    }

    #[IsGranted('ROLE_AGENT')]
    #[Route('/createProperty', name: 'createProperty')]
    public function createProperty(Request $request, CommonHelper $commonHelper, EntityManagerInterface $em, Uploader $uploader): Response
    {
        $ownerId = $request->getSession()->get('ownerId');

        $property = new Property();

        $form = $this->createForm(PropertyType::class, $property);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFiles = $form->get('propertyImage')->getData();
            if ($imageFiles) {
                $imageFileNames = [];

                foreach ($imageFiles as $file) {
                    $imageFileNames[] = $imageFileNames[] = $uploader->upload($file, CommonHelper::Property_IMAGE_UPLOAD);
                }

                $property->setPropertyImage($imageFileNames);
            }

            $property->setOwnerId($ownerId);

            $em->persist($property);

            $em->flush();

            return $this->redirectToRoute('userProperty');
        }

        return $this->render('userProperty/create_user_property.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_AGENT')]
    #[Route('/editProperty/{id}', name: 'userPropertyEdit')]
    public function userPropertyEdit(Request $request, CommonHelper $commonHelper, PropertyRepository $propertyRepository, Uploader $uploader, EntityManagerInterface $em, string $id): Response
    {
        $ownerId = $request->getSession()->get('ownerId');

        $property = $propertyRepository->getOwnerProperty($ownerId, $id);

        $form = $this->createForm(PropertyType::class, $property);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFiles = $form->get('propertyImage')->getData();

            if ($imageFiles) {
                $imageFileNames = [];

                foreach ($imageFiles as $file) {
                    $imageFileNames[] = $uploader->upload($file, CommonHelper::Property_IMAGE_UPLOAD);
                }

                $property->setPropertyImage($imageFileNames);
            }

            $property->setIsUpdatedAt(new \DateTime());

            $em->persist($property);

            $em->flush();

            return $this->redirectToRoute('userProperty');
        }

        return $this->render('userProperty/edit_user_property.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_AGENT')]
    #[Route('/propertyDelete/{id}', name: 'propertyDelete')]
    public function delete(Request $request, EntityManagerInterface $em, PropertyRepository $propertyRepository, ?string $id = null): Response
    {
        $ownerId = $request->getSession()->get('ownerId');

        $property = $propertyRepository->getOwnerProperty($ownerId, $id);

        $em->remove($property);

        $em->flush();

        return $this->redirectToRoute('userProperty');
    }
}
