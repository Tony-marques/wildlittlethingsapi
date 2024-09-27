<?php

namespace App\Controller;

use App\Entity\SubCategory;
use App\Repository\CategoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route(path: "/api", name: "api_")]
class SubCategoryController extends AbstractController
{
    #[Route('/subcategories/create', name: 'subcategory_create')]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, SluggerInterface $slugger, CategoryRepository $categoryRepository): JsonResponse
    {
        $categoryName = $request->query->get("category");

        $category = $categoryRepository->findOneBy(["slug" => $categoryName]);

        // dd($category);

        $subcategory = $serializer->deserialize($request->getContent(), SubCategory::class, "json");

        $subcategory->setCreatedAt(new DateTimeImmutable())
            ->setSlug($slugger->slug($subcategory->getName())->lower())
            ->setCategory($category);

        $em->persist($subcategory);
        $em->flush();

        $subcategoryJson = $serializer->serialize($subcategory, "json", ['groups' => ['subcategory:read']]);

        return new JsonResponse($subcategoryJson, Response::HTTP_CREATED, [], true);
    }
}
