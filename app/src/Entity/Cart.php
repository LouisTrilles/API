<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToMany(mappedBy: 'cartRef', targetEntity: CartItem::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(CartItem $item): self
    {
        foreach ($this->getItems() as $existingItems){
            if ($existingItems->equals($item)){
                $existingItems->setQuantity(
                    $existingItems->getQuantity() + $item->getQuantity()
                );
                return $this;
            }
        }
        $this->items[] = $item;
        $item->setCartRef($this);
        return $this;
    }

    public function removeItem(CartItem $item): self
    {
        foreach ($this->getItems() as $existingItems){
            if ($existingItems->equals($item)){
                $existingItems->setQuantity(
                    $existingItems->getQuantity() - 1
                );
                if ($existingItems->getQuantity() === 0){
                    if ($this->items->removeElement($item)) {
                        // set the owning side to null (unless already changed)
                        if ($item->getCartRef() === $this) {
                            $item->setCartRef(null);
                        }
                    }
                    return $this;
                }
                return $this;
            }
        }
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getCartRef() === $this) {
                $item->setCartRef(null);
            }
        }
        return $this;
    }

    public function printProductList():array
    {
        $products = [];
        if (null !== $this){
            foreach ($this->getItems() as $item){
                for ($i=0; $i < $item->getQuantity(); $i++){
                    array_push($products, $item->getProduct());
                }
            }
        }
        if (count($products) === 0){
            return [];
        }
        return $products;
    }

    public function computeTotalPrice():float
    {
        $items = $this->getItems();
        $price = 0.00;
        if (count($items) <= 0){
            return $price;
        }
        foreach ($items as $item){
            $itemPrice = $item->getProduct()->getPrice() * $item->getQuantity();
            $price += $itemPrice;
        }
        return $price;
    }

    public function emptyCart():void
    {
        $this->items->clear();
    }

}
