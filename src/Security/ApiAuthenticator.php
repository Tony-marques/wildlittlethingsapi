<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ApiAuthenticator extends AbstractAuthenticator
{
    public function __construct(private readonly UserRepository $userRepository, private readonly NormalizerInterface $normalizer, private SerializerInterface $serializer)
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->getMethod() === 'POST' && $request->getPathInfo() === '/api/login';
    }

    public function authenticate(Request $request): Passport
    {
        ['email' => $email, 'password' => $password] = $request->toArray();
        return new Passport(
            new UserBadge($email, function (string $userIdentifier): ?UserInterface {
                return $this->userRepository->findOneBy(['email' => $userIdentifier]);
            }),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // dd($token->getUser());
        return new JsonResponse(
            $this->normalizer->normalize($token->getUser(), 'json', ["groups" => "user:read"]),
            // $this->serializer->serialize($token->getUser(), 'json', ["groups" => "user:read"], false)
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);

    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
    }
}
