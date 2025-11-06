<?php

namespace App\Entity;

use App\Repository\PiezaDeArteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PiezaDeArteRepository::class)]
class PiezaDeArte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[ORM\Column(nullable: true)]
    private ?int $anio = null;

    #[ORM\ManyToOne(inversedBy: 'piezaDeArtes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Artista $artista = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getAnio(): ?int
    {
        return $this->anio;
    }

    public function setAnio(?int $anio): static
    {
        $this->anio = $anio;

        return $this;
    }

    public function getArtista(): ?Artista
    {
        return $this->artista;
    }

    public function setArtista(?Artista $artista): static
    {
        $this->artista = $artista;

        return $this;
    }
}
