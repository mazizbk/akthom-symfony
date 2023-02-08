<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Repository\MenuRepository;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[UniqueEntity(fields: ['page_id'],message : 'le lien avec la page est déja défini!')]

class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $parent_id = null;

    #[ORM\OneToOne(inversedBy: 'menu', cascade: ['persist', 'remove'])]

    private ?Page $page_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function setParentId(?int $parent_id): self
    {
        $this->parent_id = $parent_id;

        return $this;
    }

    public function getPageId(): ?Page
    {
        return $this->page_id;
    }

    public function setPageId(?Page $page_id): self
    {
        $this->page_id = $page_id;

        return $this;
    }
}
