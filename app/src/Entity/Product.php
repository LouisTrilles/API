<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\AddProductController;
use App\Controller\ListProductsFromCartController;
use App\Controller\RemoveProductController;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    collectionOperations: [
        "get" => [
            "path" => "/products"
        ],
        "post" => [
            "path" => "/product",
            "security" => "is_granted('IS_AUTHENTICATED_FULLY')"
        ],
        "listProductFromCart" => [
            "method" => "GET",
            "path" => "/cart",
            "controller" => ListProductsFromCartController::class,
            "openapi_context" => [
                "summary" => "Get all products into the user cart",
                "description" => "Get all products into the user cart",
            ]
        ]
    ],
    itemOperations: [
        "get" => [
            "path" => "/product/{id}",
            "openapi_context" => [
                "parameters" => [
                    "id" => [
                        "name" => "id",
                        "type" => "integer",
                        "required" => true,
                        "default" => 1,
                        "in" => "path",
                        "description" => "Id of a product"
                    ]
                ]
            ]
        ],
        "patch" => [
            "path" => "/product/{id}",
            "security" => "is_granted('IS_AUTHENTICATED_FULLY')"
        ],
        "delete" => [
            "path" => "/product/{id}",
            "openapi_context" => [
                "parameters" => [
                    "id" => [
                        "name" => "id",
                        "type" => "integer",
                        "required" => true,
                        "default" => 1,
                        "in" => "path",
                        "description" => "Id of a product"
                    ]
                ]
            ],
            "security" => "is_granted('IS_AUTHENTICATED_FULLY')"
        ],
        "addProduct" => [
            "method" => "GET",
            "path" => "/cart/{id}",
            "controller" => AddProductController::class,
            "openapi_context" => [
                "summary" => "Add a product into the user cart",
                "description" => "Return the product added to the cart",
                "parameters" => [
                    "id" => [
                        "name" => "id",
                        "type" => "integer",
                        "required" => true,
                        "default" => 1,
                        "in" => "path",
                        "description" => "Id of a product"
                    ]
                ]
            ]
        ],
        "removeProduct" => [
            "method" => "DELETE",
            "path" => "/cart/{id}",
            "controller" => RemoveProductController::class,
            "openapi_context" => [
                "summary" => "Remove a product into the user cart",
                "description" => "Remove a product from the cart and return 204",
                "parameters" => [
                    "id" => [
                        "name" => "id",
                        "type" => "integer",
                        "required" => true,
                        "default" => 1,
                        "in" => "path",
                        "description" => "Id of a product"
                    ]
                ]
            ]
        ]
    ],
    denormalizationContext: ["groups" => [
        "products:write"
    ]],
    normalizationContext: ["groups" => [
        "products:read"
    ]],
    paginationEnabled: false
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        "products:read",
    ])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        "products:read",
        "products:write"
    ])]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        "products:read",
        "products:write"
    ])]
    private $description;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        "products:read",
        "products:write"
    ])]
    private $photo;

    #[ORM\Column(type: 'float')]
    #[Groups([
        "products:read",
        "products:write"
    ])]
    private $price;

    #[ORM\ManyToMany(targetEntity: Cart::class, mappedBy: 'productList')]
    private $carts;

    public function __construct()
    {
        $this->carts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Cart>
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): self
    {
        if (!$this->carts->contains($cart)) {
            $this->carts[] = $cart;
            $cart->addProductList($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): self
    {
        if ($this->carts->removeElement($cart)) {
            $cart->removeProductList($this);
        }

        return $this;
    }
}
