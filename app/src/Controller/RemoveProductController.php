<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class RemoveProductController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(Product $data)
    {
        $cart = new Cart();
        $cart->removeProductList($data);
        $this->em->persist($cart);
        $this->em->flush();
        return $data;
    }
}