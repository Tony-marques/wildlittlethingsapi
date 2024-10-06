<?php

namespace App\Controller;

use App\DTO\LoginDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path: "/api", name: "api_")]
class SecurityController extends AbstractController
{
    // #[Route('/login', name: 'login', methods: ["POST"])]
    // public function login(UserRepository $userRepository, SerializerInterface $serializer, Request $request, UserPasswordHasherInterface $hasher): JsonResponse
    // {
    //     $data = $serializer->deserialize($request->getContent(), User::class, "json");

    //     $user = $userRepository->findOneByEmail($data->getEmail());

    //     if(!$user){
    //         $response = [
    //             "message" => "Email ou mot de passe incorrect"
    //         ];

    //         return new JsonResponse($response, Response::HTTP_FORBIDDEN);
    //     }

    //     if (!$hasher->isPasswordValid($user, $data->getPassword())) {
    //         $response = [
    //             "message" => "Email ou mot de passe incorrect"
    //         ];

    //         return new JsonResponse($response, Response::HTTP_FORBIDDEN);
    //     }

    //     return $this->json([
    //         "id" => $user->getId(),
    //         'email' => $user->getEmail(),
    //     ]);
    // }

        #[Route(path: '/login', name: 'app_login', methods: ['post'])]
    public function login(
        Security $security,
        #[MapRequestPayload]
        User $data,
        UserRepository $userRepository,
    ): Response
    {
        // dd($data->username);
        $user = $userRepository->findOneBy([
            'email' => $data->getEmail()
        ]);
        $security->login($user);
        return new JsonResponse([
            'auth' => 'success'
        ], Response::HTTP_OK, ["groups" => "user:read"]);
    }

    #[Route(path: "/signup", name: "signup")]
    public function signup(Request $request, SerializerInterface $serializer, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): JsonResponse {
        $data = $serializer->deserialize($request->getContent(), User::class, "json");

        $user = new User();
        $user->setEmail($data->getEmail())
            ->setPassword($hasher->hashPassword($user, $data->getPassword()));

        $em->persist($user);
        $em->flush();

        // $userJson = $serializer->serialize($user, "json");


        return new JsonResponse();
    }

    #[Route(path: "/me", name: 'me')]
    public function me(
        #[CurrentUser]
        User $user,
        NormalizerInterface $normalizer,
    ): JsonResponse
    {
        return new JsonResponse($normalizer->normalize($user, 'json', ["groups" => "user:read"]));
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
    }
}
