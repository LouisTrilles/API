<?php

namespace App\Controller;

use App\Entity\Cart;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ListProductsFromCartController
{
    public function __invoke(): Collection
    {
        $cart = new Cart();
        return $cart->getProductList();
    }
}