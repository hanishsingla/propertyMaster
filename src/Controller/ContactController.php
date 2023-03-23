<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, EntityManagerInterface $em,): Response
    {
        $helpData = new Contact();
        $form = $this->createForm(ContactType::class, $helpData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($helpData);

            $em->flush();

            return $this->redirectToRoute('app_contact');
        }
        return $this->render('contact/contact.html.twig',[
            'helpData' => $form->createView(),
        ]);
    }
}