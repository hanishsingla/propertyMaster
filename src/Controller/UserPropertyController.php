<?php

namespace App\Controller;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use App\Service\CommonHelper;
use App\Service\PropertyUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserPropertyController extends AbstractController
{

    #[IsGranted('ROLE_USER')]
    #[Route('/userProperty', name: 'userProperty')]
    public function userProperty(Request $request,PropertyRepository $propertyRepository): Response
    {
        $ownerId = $request->getSession()->get('ownerId');
       $propertyLists = $propertyRepository->findBy(['ownerId' => $ownerId]);

      return   $this->render('userProperty/user_property.html.twig',[
         'propertyLists' => $propertyLists,
      ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/createProperty', name: 'createProperty')]
    public function createProperty(Request $request,CommonHelper $commonHelper,EntityManagerInterface $em, PropertyUploader $propertyUploader)
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
            $commonHelper->setInformation($form, $ownerId);

            $em->persist($propertyForm);

            $em->flush();

            return $this->redirectToRoute('createProperty');
        }
        return $this->render('form/user_property_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/editProperty/{id}', name: 'userPropertyEdit')]
    public function userPropertyEdit(Request $request, PropertyRepository $propertyRepository, EntityManagerInterface $em, $id):Response
    {

        $ownerId = $request->getSession()->get('ownerId');
        $userPropertyEditData = $propertyRepository->findOneBy(['id'=> $id,'ownerId'=> $ownerId]);

        $form = $this->createForm(PropertyType::class, $userPropertyEditData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($userPropertyEditData);

            $em->flush();

            return $this->redirectToRoute('userProperty');
        }

        return $this->render('form/user_property_form.html.twig',[
            'form' => $form->createView(),
        ]);
    }
    #[Route('/propertyDelete{id}', name: 'propertyDelete')]
    public function delete(Request $request,EntityManagerInterface $em, PropertyRepository $propertyRepository, $id = null): Response
    {
        $ownerId = $request->getSession()->get('ownerId');
        $propertyData = $propertyRepository ->findOneBy(['id'=>$id,'ownerId'=>$ownerId]);
        $em -> remove($propertyData);
        $em -> flush();
        return $this->redirectToRoute('userProperty');
    }
}