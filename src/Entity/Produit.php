<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 */
class Produit
{
  /**
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=255)
   * @Assert\Length(
   * min = 1,
   * max = 255
   * )
   */
  private $name;

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  private $photo;

  /**
   * @ORM\Column(type="integer")
   * @Assert\NotNull
   * @Assert\GreaterThan(value = 0)
   */
  private $stock;

  /**
   * @ORM\Column(type="integer")
   * @Assert\GreaterThanOrEqual(value = 0)
   */
  private $prix;

  /**
   * @ORM\OneToMany(targetEntity="App\Entity\Panier", mappedBy="produit", orphanRemoval=true)
   */
  private $paniers;

  public function __construct()
  {
    $this->paniers = new ArrayCollection();
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

  public function getPhoto(): ?string
  {
    return $this->photo;
  }

  public function setPhoto(?string $photo): self
  {
    $this->photo = $photo;

    return $this;
  }

  public function getStock(): ?int
  {
    return $this->stock;
  }

  public function setStock(int $stock): self
  {
    $this->stock = $stock;

    return $this;
  }

  public function getPrix(): ?int
  {
    return $this->prix;
  }

  public function setPrix(int $prix): self
  {
    $this->prix = $prix;

    return $this;
  }

  /**
   * @return Collection|Panier[]
   */
  public function getPaniers(): Collection
  {
    return $this->paniers;
  }

  public function addPanier(Panier $panier): self
  {
    if (!$this->paniers->contains($panier)) {
      $this->paniers[] = $panier;
      $panier->setProduit($this);
    }

    return $this;
  }

  public function removePanier(Panier $panier): self
  {
    if ($this->paniers->contains($panier)) {
      $this->paniers->removeElement($panier);
      // set the owning side to null (unless already changed)
      if ($panier->getProduit() === $this) {
        $panier->setProduit(null);
      }
    }

    return $this;
  }
}
