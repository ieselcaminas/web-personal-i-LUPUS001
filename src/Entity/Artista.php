<?php

namespace App\Entity;

use App\Repository\ArtistaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistaRepository::class)]
class Artista
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nacionalidad = null;

    #[ORM\Column(length: 255)]
    private ?string $movimiento = null;

    /**
     * @var Collection<int, PiezaDeArte>
     */
    #[ORM\OneToMany(targetEntity: PiezaDeArte::class, mappedBy: 'artista')]
    private Collection $piezaDeArtes;

    public function __construct()
    {
        $this->piezaDeArtes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getNacionalidad(): ?string
    {
        return $this->nacionalidad;
    }

    public function setNacionalidad(?string $nacionalidad): static
    {
        $this->nacionalidad = $nacionalidad;

        return $this;
    }

    public function getMovimiento(): ?string
    {
        return $this->movimiento;
    }

    public function setMovimiento(string $movimiento): static
    {
        $this->movimiento = $movimiento;

        return $this;
    }

    /**
     * @return Collection<int, PiezaDeArte>
     */
    public function getPiezaDeArtes(): Collection
    {
        return $this->piezaDeArtes;
    }

    public function addPiezaDeArte(PiezaDeArte $piezaDeArte): static
    {
        if (!$this->piezaDeArtes->contains($piezaDeArte)) {
            $this->piezaDeArtes->add($piezaDeArte);
            $piezaDeArte->setArtista($this);
        }

        return $this;
    }

    public function removePiezaDeArte(PiezaDeArte $piezaDeArte): static
    {
        if ($this->piezaDeArtes->removeElement($piezaDeArte)) {
            // set the owning side to null (unless already changed)
            if ($piezaDeArte->getArtista() === $this) {
                $piezaDeArte->setArtista(null);
            }
        }

        return $this;
    }
}
