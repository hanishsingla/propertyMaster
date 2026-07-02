<?php

namespace App\Controller\Api;

use App\Api\ApiController;
use App\Api\Presenter\UserPresenter;
use App\Entity\Security\User;
use App\Enum\Gender;
use App\Service\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/account')]
#[IsGranted('ROLE_USER')]
class AccountApiController extends ApiController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPresenter $userPresenter,
    ) {
    }

    #[Route('', name: 'api_account_get', methods: ['GET'])]
    public function show(#[CurrentUser] User $user): JsonResponse
    {
        return new JsonResponse($this->userPresenter->self($user));
    }

    #[Route('', name: 'api_account_update', methods: ['PATCH'])]
    public function update(#[CurrentUser] User $user, Request $request): JsonResponse
    {
        $data = $this->decode($request);

        foreach (['name', 'phone', 'mobile', 'country', 'address', 'address2', 'city', 'state', 'zip'] as $field) {
            if (array_key_exists($field, $data)) {
                $user->{'set'.ucfirst($field)}(null === $data[$field] ? null : (string) $data[$field]);
            }
        }
        if (array_key_exists('gender', $data)) {
            $user->setGender($data['gender'] ? Gender::tryFrom((string) $data['gender']) : null);
        }

        $this->em->flush();

        return new JsonResponse($this->userPresenter->self($user));
    }

    #[Route('/avatar', name: 'api_account_avatar', methods: ['POST'])]
    public function avatar(#[CurrentUser] User $user, Request $request, Uploader $uploader): JsonResponse
    {
        $file = $request->files->get('avatar');
        if (null === $file) {
            return new JsonResponse([
                'error' => ['code' => 'validation_failed', 'message' => 'No avatar file provided.'],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $filename = $uploader->upload($file, '/uploads/avatars');
        $user->setAvatar($filename);
        $this->em->flush();

        return new JsonResponse(['avatarUrl' => $this->userPresenter->self($user)['avatarUrl']]);
    }

    #[Route('/change-password', name: 'api_account_change_password', methods: ['POST'])]
    public function changePassword(
        #[CurrentUser] User $user,
        Request $request,
        UserPasswordHasherInterface $hasher,
    ): JsonResponse {
        $data = $this->decode($request);
        $current = (string) ($data['currentPassword'] ?? '');
        $new = (string) ($data['newPassword'] ?? '');

        if (!$hasher->isPasswordValid($user, $current)) {
            return new JsonResponse([
                'error' => ['code' => 'invalid_credentials', 'message' => 'Current password is incorrect.'],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if (strlen($new) < 8) {
            return new JsonResponse([
                'error' => ['code' => 'validation_failed', 'message' => 'New password must be at least 8 characters.',
                    'violations' => [['field' => 'newPassword', 'message' => 'Must be at least 8 characters.']]],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->setPassword($hasher->hashPassword($user, $new));
        $this->em->flush();

        return $this->noContent();
    }

    #[Route('', name: 'api_account_delete', methods: ['DELETE'])]
    public function delete(
        #[CurrentUser] User $user,
        Request $request,
        UserPasswordHasherInterface $hasher,
        Security $security,
    ): JsonResponse {
        $data = $this->decode($request);
        if (!$hasher->isPasswordValid($user, (string) ($data['password'] ?? ''))) {
            return new JsonResponse([
                'error' => ['code' => 'invalid_credentials', 'message' => 'Password is incorrect.'],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Anonymize PII but keep the row (frees the unique email for re-registration).
        $user->setEmail('deleted+'.$user->getId().'@local.invalid')
            ->setName(null)->setPhone(null)->setMobile(null)
            ->setAddress(null)->setAddress2(null)->setCity(null)
            ->setState(null)->setZip(null)->setCountry(null)
            ->setAvatar(null)->setGender(null)
            ->setRoles([])->setIsAgent(false);
        $user->softDelete();

        // Cascade soft-delete to the user's properties.
        foreach ($this->em->getRepository(\App\Entity\Property\Property::class)->findBy(['owner' => $user, 'deletedAt' => null]) as $property) {
            $property->softDelete();
        }

        $this->em->flush();
        $security->logout(false);

        return $this->noContent();
    }
}
