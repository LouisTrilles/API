<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    private ProductRepository $repository;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private EntityManagerInterface $em;

    /**
     * @param ProductRepository $repository
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $em
     */
    public function __construct(ProductRepository $repository, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->em = $em;
    }


    #[Route('/api/products', name: 'app_product_all', methods: "GET")]
    public function getAllProducts(): Response
    {
        $products = $this->repository->findAll();
        $data = $this->serializer->serialize($products, "json", [
            "groups" => "products:read"
        ]);
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/api/product/{productId}', name: 'app_product_one', methods: "GET")]
    public function getOneProduct(int $productId): Response
    {
        $product = $this->repository->findOneById($productId);
        if (null === $product){
            throw $this->createNotFoundException("Product Not Found");
        }
        $data = $this->serializer->serialize($product, "json", [
            "groups" => "products:read"
        ]);
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/api/products', name: 'app_product_create', methods: "POST")]
    public function createOneProduct(Request $request): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $product = $this->serializer->deserialize($request->getContent(), Product::class, "json");
        $validationErrors = $this->validator->validate($product);
        count($validationErrors) > 0 ? throw new ValidationFailedException("Validation Failed", $validationErrors): null;
        $this->em->persist($product);
        $this->em->flush();
        $data = $this->serializer->serialize($product, "json", [
            "groups" => "products:read"
        ]);
        return new JsonResponse($data, 201, [], true);
    }

    #[Route('/api/product/{productId}', name: 'app_product_delete', methods: "DELETE")]
    public function deleteOneProduct(int $productId): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $product = $this->repository->findOneById($productId);
        if (null === $product){
            throw $this->createNotFoundException("Product Not Found");
        }
        $this->em->remove($product);
        $this->em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT, []);
    }

    #[Route('/api/product/{productId}', name: 'app_product_update', methods: "PATCH")]
    public function updateOneProduct(int $productId, Request $request): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $product = $this->repository->findOneById($productId);
        if (null === $product){
            throw $this->createNotFoundException("Product Not Found");
        }
        $updatedProduct = $this->serializer->deserialize($request->getContent(), Product::class, "json", [
            AbstractNormalizer::OBJECT_TO_POPULATE => $product
        ]);
        $errors = $this->validator->validate($updatedProduct);
        if (count($errors) > 0){
            throw new ValidationFailedException("Validation Failed", $errors);
        }
        $this->em->flush();
        $data = $this->serializer->serialize($updatedProduct, "json", [
            AbstractNormalizer::GROUPS => "products:read"
        ]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
