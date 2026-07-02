<?php

namespace App\Controller\Api;

use App\Api\ApiController;
use App\Api\Dto\RegisterInput;
use App\Api\Presenter\UserPresenter;
use App\Entity\Security\User;
use App\Repository\Security\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

#[Route('/api')]
class AuthApiController extends ApiController
{
    public function __construct(private readonly UserPresenter $userPresenter)
    {
    }

    // Intercepted by the firewall's json_login; never actually executed.
    #[Route('/auth/login', name: 'api_auth_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        return new JsonResponse(['error' => ['code' => 'error', 'message' => 'Authentication failed.']], 401);
    }

    // Intercepted by the firewall's logout listener; never executed.
    #[Route('/auth/logout', name: 'api_auth_logout', methods: ['POST'])]
    public function logout(): void
    {
    }

    #[Route('/me', name: 'api_me', methods: ['GET'])]
    public function me(#[\Symfony\Component\Security\Http\Attribute\CurrentUser] ?User $user): JsonResponse
    {
        return new JsonResponse(['user' => $user ? $this->userPresenter->self($user) : null]);
    }

    #[Route('/csrf', name: 'api_csrf', methods: ['GET'])]
    public function csrf(CsrfTokenManagerInterface $csrf): JsonResponse
    {
        return new JsonResponse(['token' => $csrf->getToken('api')->getValue()]);
    }

    #[Route('/auth/register', name: 'api_auth_register', methods: ['POST'])]
    public function register(
        Request $request,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        EmailVerifier $emailVerifier,
        Security $security,
        RateLimiterFactory $registrationLimiter,
    ): JsonResponse {
        $this->limit($registrationLimiter, $request);

        $data = $this->decode($request);
        $input = new RegisterInput();
        $input->email = isset($data['email']) ? (string) $data['email'] : null;
        $input->password = isset($data['password']) ? (string) $data['password'] : null;
        $input->name = isset($data['name']) ? (string) $data['name'] : null;
        $input->isAgent = (bool) ($data['isAgent'] ?? false);

        $this->assertValid($validator->validate($input), $input);

        if (null !== $userRepository->findOneBy(['email' => $input->email])) {
            return new JsonResponse([
                'error' => ['code' => 'conflict', 'message' => 'An account with this email already exists.'],
            ], Response::HTTP_CONFLICT);
        }

        $user = (new User())
            ->setEmail($input->email)
            ->setName($input->name)
            ->setIsAgent($input->isAgent);
        $user->setPassword($hasher->hashPassword($user, $input->password));
        $em->persist($user);
        $em->flush();

        $this->sendVerificationEmail($emailVerifier, $user);

        // Log the user in immediately (verified-email gate limits what they can do).
        $security->login($user);

        return new JsonResponse($this->userPresenter->self($user), Response::HTTP_CREATED);
    }

    #[Route('/auth/verify-email', name: 'api_auth_verify_email', methods: ['GET'])]
    public function verifyEmail(Request $request, EmailVerifier $emailVerifier, UserRepository $userRepository): RedirectResponse
    {
        // The signed link carries the user id in the "id" query param.
        $user = $userRepository->find((string) $request->query->get('id'));
        if (null === $user) {
            return new RedirectResponse('/email-verified?status=invalid');
        }

        try {
            $emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface) {
            return new RedirectResponse('/email-verified?status=invalid');
        }

        return new RedirectResponse('/email-verified?status=ok');
    }

    #[Route('/auth/resend-verification', name: 'api_auth_resend_verification', methods: ['POST'])]
    public function resendVerification(#[\Symfony\Component\Security\Http\Attribute\CurrentUser] ?User $user, EmailVerifier $emailVerifier): JsonResponse
    {
        if (null === $user) {
            throw new AccessDeniedException();
        }
        if (!$user->isVerified()) {
            $this->sendVerificationEmail($emailVerifier, $user);
        }

        return $this->noContent();
    }

    #[Route('/auth/reset-password/request', name: 'api_auth_reset_request', methods: ['POST'])]
    public function resetRequest(
        Request $request,
        UserRepository $userRepository,
        ResetPasswordHelperInterface $resetPasswordHelper,
        EmailVerifier $emailVerifier,
        RateLimiterFactory $resetPasswordLimiter,
    ): JsonResponse {
        $this->limit($resetPasswordLimiter, $request);

        $data = $this->decode($request);
        $email = isset($data['email']) ? (string) $data['email'] : '';
        $user = $userRepository->findOneBy(['email' => $email]);

        // Never reveal whether the account exists.
        if (null !== $user) {
            try {
                $resetToken = $resetPasswordHelper->generateResetToken($user);
                $emailVerifier->processSendingPasswordResetEmail($user, $resetToken);
            } catch (ResetPasswordExceptionInterface|TransportExceptionInterface) {
                // Swallow — still return 204.
            }
        }

        return $this->noContent();
    }

    #[Route('/auth/reset-password/reset', name: 'api_auth_reset_reset', methods: ['POST'])]
    public function resetReset(
        Request $request,
        ResetPasswordHelperInterface $resetPasswordHelper,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em,
    ): JsonResponse {
        $data = $this->decode($request);
        $token = isset($data['token']) ? (string) $data['token'] : '';
        $password = isset($data['password']) ? (string) $data['password'] : '';

        if (strlen($password) < 8) {
            return new JsonResponse([
                'error' => ['code' => 'validation_failed', 'message' => 'Password must be at least 8 characters.',
                    'violations' => [['field' => 'password', 'message' => 'Password must be at least 8 characters.']]],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $user = $resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface) {
            return new JsonResponse([
                'error' => ['code' => 'invalid_token', 'message' => 'This reset link is invalid or has expired.'],
            ], Response::HTTP_BAD_REQUEST);
        }

        $resetPasswordHelper->removeResetRequest($token);
        $user->setPassword($hasher->hashPassword($user, $password));
        $em->flush();

        return $this->noContent();
    }

    private function sendVerificationEmail(EmailVerifier $emailVerifier, User $user): void
    {
        try {
            $emailVerifier->sendEmailConfirmation(
                'api_auth_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@propertymaster.test', 'PropertyMaster'))
                    ->to($user->getEmail())
                    ->subject('Please confirm your email')
                    ->htmlTemplate('security/registration/confirmation_email.html.twig')
            );
        } catch (TransportExceptionInterface) {
            // Email delivery failure must not block registration.
        }
    }

    private function limit(RateLimiterFactory $factory, Request $request): void
    {
        if (!$factory->create($request->getClientIp())->consume(1)->isAccepted()) {
            throw new \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException();
        }
    }
}
