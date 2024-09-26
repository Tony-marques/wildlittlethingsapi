<?php

namespace App\Controller;

use App\Entity\Category;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: "/api", name: "api_")]
class CategoryController extends AbstractController
{
    #[Route('/categories/create', name: 'category_create')]
    public function index(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $category = $serializer->deserialize($request->getContent(), Category::class, "json");

        $category->setCreatedAt(new DateTimeImmutable());

        $em->persist($category);
        $em->flush();

        $categoryJson = $serializer->serialize($category, "json");

        return new JsonResponse($categoryJson, Response::HTTP_CREATED,  [], true);
    }
}
