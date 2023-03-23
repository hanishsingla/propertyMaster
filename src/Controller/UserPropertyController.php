<?php

namespace App\Controller;

use App\Entity\Property;
use App\Form\PropertyType;
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
    public function userProperty(Request $request, CommonHelper $commonHelper, EntityManagerInterface $em, PropertyUploader $propertyUploader): Response
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

            return $this->redirectToRoute('userProperty');
        }
        return $this->render('user_property.html.twig', [
            'propertyForm' => $form->createView(),
        ]);
    }

}