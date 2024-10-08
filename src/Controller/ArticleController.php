<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\SubCategoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: "/api", name: "api_")]
class ArticleController extends AbstractController
{
    #[Route(path: '/articles', name: 'articles')]
    public function index(ArticleRepository $articleRepository, SerializerInterface $serializer, Request $request, CategoryRepository $categoryRepository, SubCategoryRepository $subCategoryRepository): JsonResponse
    {
        $categoryName = $request->query->get('category');

        $category = $categoryRepository->findOneBy(["slug" => $categoryName]);

        $subcategoryName = $request->query->get('subcategory');

        $subcategory = $subCategoryRepository->findOneBy(["slug" => $subcategoryName]);


        if($category) {
            $articlesJson = $serializer->serialize($category->getArticles(), "json", ['groups' => ['article:read']]);

            return new JsonResponse($articlesJson, Response::HTTP_OK, [], true);
        }

        if($subcategory) {
            $articlesJson = $serializer->serialize($subcategory->getArticles(), "json", ['groups' => ['article:read', ]]);

            return new JsonResponse($articlesJson, Response::HTTP_OK, [], true);
        }

        $articles = $articleRepository->findBy([], ["createdAt" => "DESC"]);

        $articlesJson = $serializer->serialize($articles, "json", ['groups' => ['article:read']]);

        return new JsonResponse($articlesJson, Response::HTTP_OK, [], true);
    }


    #[Route(path: "/articles/create", name: "article_create")]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator, SluggerInterface $slugger, CategoryRepository $categoryRepository, SubCategoryRepository $subCategoryRepository)
    {
        $data = $request->request->all();

        // dd($data);

        $category = $categoryRepository->findOneBy(["slug" => $data["category"]]);

        $article = new Article();
        $article->setTitle($data['title'] ?? '')
            ->setDescription($data['description'] ?? '')
            ->setContent($data['content'] ?? '')
            ->setDestination($data['destination'] ?? '')
            ->setCategory($category);

        if($category->getSubCategories()->count() > 0) {
            $subcategory = $subCategoryRepository->findOneBy(["slug" => $data["subcategory"]]);
            $article->setSubCategory($subcategory);
        }

        if ($request->files->has('mainImage1')) {
            $mainImage1File = $request->files->get('mainImage1');
            if ($mainImage1File instanceof UploadedFile) {
                $originalFilename = pathinfo($mainImage1File->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename1 = $safeFilename . '-' . uniqid() . '.' . $mainImage1File->guessExtension();

                try {
                    $mainImage1File->move(
                        "uploads/images",
                        $newFilename1
                    );
                    $article->setMainImage1($newFilename1);
                } catch (FileException $e) {
                    return new JsonResponse(['error' => 'Une erreur est survenue lors du téléchargement de l\'image 1'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        } else {
            $article->setMainImage1("");
        }

        if ($request->files->has('mainImage2')) {
            $mainImage2File = $request->files->get('mainImage2');
            if ($mainImage2File instanceof UploadedFile) {
                $originalFilename = pathinfo($mainImage2File->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename2 = $safeFilename . '-' . uniqid() . '.' . $mainImage2File->guessExtension();

                try {
                    $mainImage2File->move(
                        "uploads/images",
                        $newFilename2
                    );
                    $article->setMainImage2($newFilename2);
                } catch (FileException $e) {
                    // Gérer l'exception
                    return new JsonResponse(['error' => 'Une erreur est survenue lors du téléchargement de l\'image 2'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        }

        $errors = $validator->validate($article);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $article->setCreatedAt(new DateTimeImmutable())
        ->setSlug($slugger->slug($article->getTitle())->lower());

        $em->persist($article);
        $em->flush();

        $articleJson = $serializer->serialize($article, "json", ['groups' => ['article:read']]);

        return new JsonResponse($articleJson, Response::HTTP_CREATED, [], true);
    }


    #[Route(path: "/articles/{slug}")]
    public function getArticle(string $slug, ArticleRepository $articleRepository, SerializerInterface $serializer)
    {
        $article = $articleRepository->findOneBySlug($slug);

        $articleJson = $serializer->serialize($article, "json", ["groups" => "article:read"]);

        return new JsonResponse($articleJson, Response::HTTP_OK, [], true);
    }
}
