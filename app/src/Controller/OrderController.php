<?php

namespace App\Controller;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\OrderRepository;

class OrderController extends AbstractController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer){
        $this->serializer = $serializer;
    }

    #[Route('/api/orders', name: 'app_orders', methods: "GET")]
    public function getOrders(OrderRepository $repo): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $orders = $this->getUser()->getOrders();
        if(!$orders || count($orders) === 0){
            throw $this->createNotFoundException("No orders found for this user");
        }
        $data = $this->serializer->serialize($orders, "json", [
            AbstractNormalizer::GROUPS => [
                "products:read",
                "order:read",
            ]
        ]);
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/api/orders/{orderId}', name: 'app_orders_details', methods: "GET")]
    public function getOrdersDetails(OrderRepository $repo, int $orderId): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        $order = $repo->findOneById($orderId);
        if(!$order){
            throw $this->createNotFoundException("This order doesn't exist");
        }
        if ($order->getUser() !== $this->getUser()){
            throw $this->createAccessDeniedException("This order doesn't belong to you");
        }
        $data = $this->serializer->serialize($order, "json", [
            AbstractNormalizer::GROUPS => [
                "products:read",
                "order:details:read",
            ]
        ]);
        return new JsonResponse($data, 200, [], true);
    }
}
