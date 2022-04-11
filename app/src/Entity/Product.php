<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    collectionOperations: [
    "get" => [
        "path" => "/products"
    ],
    "post" => [
        "path" => "/product",
        "status" => 200,
        "security" => "is_granted('IS_AUTHENTICATED_FULLY')"
    ]
], itemOperations: [
    "get" => [
        "path" => "/product/{productId}",
        "status" => 200,
    ],
    "put" => [
        "path" => "/product/{productId}",
        "status" => 201,
        "security" => "is_granted('IS_AUTHENTICATED_FULLY')"
    ],
    "delete" => [
    "path" => "/product/{productId}",
    "status" => 201,
    "security" => "is_granted('IS_AUTHENTICATED_FULLY')"
]

])]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $description;

    #[ORM\Column(type: 'string', length: 255)]
    private $photo;

    #[ORM\Column(type: 'float')]
    private $price;

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
}
