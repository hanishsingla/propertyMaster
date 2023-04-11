<?php

namespace App\Controller\Property;

use App\Entity\Property\Property;
use App\Form\PropertyType;
use App\Repository\Property\PropertyRepository;
use App\Service\UploadHelper\PropertyUploader;
use App\Service\CommonHelper;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserPropertyController extends AbstractController
{

    #[IsGranted('ROLE_AGENT')]
    #[Route('/userProperty', name: 'userProperty')]
    public function userProperty(Request $request, PropertyRepository $propertyRepository): Response
    {
        $ownerId = $request->getSession()->get('ownerId');

        $propertyLists = $propertyRepository->getProperty( $ownerId);

        return $this->render('userProperty/user_property.html.twig', [
            'propertyLists' => $propertyLists,
        ]);
    }

    #[IsGranted('ROLE_AGENT')]
    #[Route('/createProperty', name: 'createProperty')]
    public function createProperty(Request $request, CommonHelper $commonHelper, EntityManagerInterface $em, PropertyUploader $propertyUploader) :Response
    {
        $ownerId = $request->getSession()->get('ownerId');


        $propertyForm = new Property();
        $form = $this->createForm(PropertyType::class, $propertyForm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $brochureFile */
            $imageFiles = $form->get('propertyImage')->getData();
            if ($imageFiles) {
                $imageFileNames = [];
                foreach ($imageFiles as $file) {
                    $imageFileNames[] = $propertyUploader->upload($file);
                }
                $propertyForm->setPropertyImage($imageFileNames);
            }

            $commonHelper->setPropertyInformation($form, $ownerId);

            $em->persist($propertyForm);

            $em->flush();

            return $this->redirectToRoute('userProperty');
        }
        return $this->render('userProperty/create_user_property.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_AGENT')]
    #[Route('/editProperty/{id}', name: 'userPropertyEdit')]
    public function userPropertyEdit(Request $request, CommonHelper $commonHelper, PropertyRepository $propertyRepository, PropertyUploader $propertyUploader, EntityManagerInterface $em, $id): Response
    {
        $ownerId = $request->getSession()->get('ownerId');


        $userPropertyEditData = $propertyRepository->findOneBy(['id' => $id, 'ownerId' => $ownerId]);
        $form = $this->createForm(PropertyType::class, $userPropertyEditData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $brochureFile */
            $imageFiles = $form->get('propertyImage')->getData();
            if ($imageFiles) {
                $imageFileNames = [];
                foreach ($imageFiles as $file) {
                    $imageFileNames[] = $propertyUploader->upload($file);
                }
                $userPropertyEditData->setPropertyImage($imageFileNames);
            }
            $commonHelper->setUpdateDate($form,);

            $em->persist($userPropertyEditData);

            $em->flush();

            return $this->redirectToRoute('userProperty');
        }

        return $this->render('userProperty/edit_user_property.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_AGENT')]
    #[Route('/propertyDelete{id}', name: 'propertyDelete')]
    public function delete(Request $request, EntityManagerInterface $em, PropertyRepository $propertyRepository, $id = null): Response
    {
        $ownerId = $request->getSession()->get('ownerId');
        $propertyData = $propertyRepository->findOneBy(['id' => $id, 'ownerId' => $ownerId]);
        $em->remove($propertyData);
        $em->flush();
        return $this->redirectToRoute('userProperty');
    }
}