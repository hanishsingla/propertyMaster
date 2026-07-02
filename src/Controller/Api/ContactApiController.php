<?php

namespace App\Controller\Api;

use App\Api\ApiController;
use App\Entity\Account\Contact;
use App\Entity\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/contact')]
class ContactApiController extends ApiController
{
    #[Route('', name: 'api_contact', methods: ['POST'])]
    public function submit(
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        RateLimiterFactory $contactLimiter,
        #[CurrentUser] ?User $user,
    ): JsonResponse {
        if (!$contactLimiter->create($request->getClientIp())->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }

        $data = $this->decode($request);

        // Honeypot: bots fill hidden fields. Pretend success without persisting.
        if (!empty($data['website'])) {
            return $this->noContent();
        }

        $name = (string) ($data['name'] ?? '');
        $email = (string) ($data['email'] ?? '');
        $message = (string) ($data['message'] ?? '');

        $violations = $validator->validate(
            ['name' => $name, 'email' => $email, 'message' => $message],
            new Assert\Collection([
                'name' => [new Assert\NotBlank(), new Assert\Length(max: 255)],
                'email' => [new Assert\NotBlank(), new Assert\Email()],
                'message' => [new Assert\NotBlank(), new Assert\Length(min: 5, max: 5000)],
            ])
        );
        $this->assertValid($violations);

        $contact = (new Contact())
            ->setName($name)
            ->setEmail($email)
            ->setMessage($message)
            ->setUser($user);
        $em->persist($contact);
        $em->flush();

        return $this->noContent();
    }
}
