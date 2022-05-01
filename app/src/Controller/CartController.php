<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CartController extends AbstractController
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

    #[Route('api/carts/{productId}', name: 'app_product_cart_add', methods: "PATCH")]
    public function addProduct(int $productId): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $currentUser = $this->getUser();
        $productToAdd = $this->repository->findOneById($productId);
        if (!$productToAdd){throw $this->createNotFoundException("Product not found");}
        $this->addProductToCart($currentUser, $productToAdd);
        $data = $this->serializer->serialize($productToAdd, "json", [
            AbstractNormalizer::GROUPS => "products:read"
        ]);
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('api/carts/{productId}', name: 'app_product_cart_remove', methods: "DELETE")]
    public function removeProduct(int $productId, CartItemRepository $repository): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $currentUser = $this->getUser();
        $productToRemove = $repository->findWithProductId($productId);
        if (!$productToRemove){
            throw $this->createNotFoundException("Product not found in the cart");
        }
        $this->removeProductFromCart($currentUser, $productToRemove);
        $data = $this->serializer->serialize($productToRemove->getProduct(), "json", [
            AbstractNormalizer::GROUPS => "products:read"
        ]);
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('api/carts', name: 'app_product_cart', methods: "GET")]
    public function getProductFromCart(CartItemRepository $repository): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $user = $this->getUser();
        $cart = $user->getCart();
        $products = $cart->printProductList();

        if (count($products) === 0 || $products === null)
        {
            throw $this->createNotFoundException();
        }

        $data = $this->serializer->serialize($products, "json", [
            AbstractNormalizer::GROUPS => "products:read"
        ]);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('api/cart/validate', name: 'app_cart_validate', methods: "GET")]
    public function validCart():Response{
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $order=new Order();
        $cart = $this->getUser()->getCart();
        $order->setTotalPrice($cart->computeTotalPrice())
            ->setCreationDate(new \DateTime("now"))
            ->setProducts($cart->printProductList())
            ->setUser($this->getUser());
        $cart->emptyCart();
        $this->em->persist($order);
        $this->em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    private function addProductToCart(UserInterface $user, Product $productToAdd):void
    {
        if (null === $user->getCart()){
            $cart = new Cart();
            $user->setCart($cart);
            $this->em->persist($cart);
            $this->em->flush();
        }
        $currentCart = $user->getCart();
        $cartItem = new CartItem();
        $cartItem->setProduct($productToAdd)
            ->setQuantity(1);
        $currentCart->addItem($cartItem);
        $this->em->flush();
    }

    private function removeProductFromCart(UserInterface $user, CartItem $productToRemove):void
    {
        if (null === $user->getCart()){
            throw $this->createNotFoundException("Cart Not Found");
        }
        $currentCart = $user->getCart();
        $currentCart->removeItem($productToRemove);
        $this->em->flush();
    }
}
