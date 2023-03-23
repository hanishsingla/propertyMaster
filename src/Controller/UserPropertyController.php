<?php

namespace App\Controller;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\AgentRepository;
use App\Repository\PropertyRepository;
use App\Service\CommonHelper;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserPropertyController extends AbstractController
{

    #[IsGranted('ROLE_USER')]
    #[Route('/userProperty', name: 'userProperty')]
    public function userProperty(Request $request,CommonHelper $commonHelper, EntityManagerInterface $em, PropertyRepository $propertyRepository):Response{
        $ownerId = $request->getSession()->get('ownerId');

        $propertyForm = new Property();
        $form = $this->createForm(PropertyType::class, $propertyForm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $commonHelper->setInformation($form,$ownerId);

            $em->persist($propertyForm);

            $em->flush();

            return $this->redirectToRoute('userProperty');
        }
        return $this->render('user_property.html.twig',[
            'propertyForm' => $form->createView(),
        ]);
    }

}