<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
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
    #[Route(path: '/categories', name: 'categories')]
    public function index(CategoryRepository $categoryRepository, SerializerInterface $serializer): JsonResponse
    {
        $categories = $categoryRepository->findBy([], ["createdAt" => "DESC"]);

        $categoriesJson = $serializer->serialize($categories, "json", ['groups' => ['category:read']]);

        return new JsonResponse($categoriesJson, Response::HTTP_OK, [], true);
    }

    #[Route('/categories/create', name: 'category_create')]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $category = $serializer->deserialize($request->getContent(), Category::class, "json");

        $category->setCreatedAt(new DateTimeImmutable());

        $em->persist($category);
        $em->flush();

        $categoryJson = $serializer->serialize($category, "json",  ['groups' => ['category:read']]);

        return new JsonResponse($categoryJson, Response::HTTP_CREATED,  [], true);
    }
}
