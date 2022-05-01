<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
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
    #[Assert\NotBlank(message: "can't be blank")]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        "products:read",
        "products:write"
    ])]
    #[Assert\NotBlank(message: "can't be blank")]
    private $description;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        "products:read",
        "products:write"
    ])]
    #[Assert\NotBlank(message: "can't be blank")]
    private $photo;

    #[ORM\Column(type: 'float')]
    #[Groups([
        "products:read",
        "products:write"
    ])]
    #[Assert\NotBlank(message: "can't be blank")]
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
