<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'id')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $cantidad = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $stock = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $precio = null;

    #[ORM\Column(length: 3)]
    private ?string $unidad = null;

    #[ORM\ManyToOne(targetEntity: Productos::class, inversedBy: 'stocks')]
    #[ORM\JoinColumn(name:'id_producto',nullable: false, referencedColumnName:'id')]
    private ?Productos $id_producto = null;

    #[ORM\ManyToOne(targetEntity: Almacenes::class, inversedBy: 'stocks')]
    #[ORM\JoinColumn(name: 'id_almacen', nullable: false, referencedColumnName:'id')]
    private ?Almacenes $id_almacen = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getCantidad(): ?string
    {
        return $this->cantidad;
    }

    public function setCantidad(string $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getStock(): ?string
    {
        return $this->stock;
    }

    public function setStock(string $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getPrecio(): ?string
    {
        return $this->precio;
    }

    public function setPrecio(string $precio): self
    {
        $this->precio = $precio;

        return $this;
    }

    public function getUnidad(): ?string
    {
        return $this->unidad;
    }

    public function setUnidad(string $unidad): self
    {
        $this->unidad = $unidad;

        return $this;
    }

    public function getIdProducto(): ?Productos
    {
        return $this->id_producto;
    }

    public function setIdProducto(?Productos $id_producto): self
    {
        $this->id_producto = $id_producto;

        return $this;
    }

    public function getIdAlmacen(): ?Almacenes
    {
        return $this->id_almacen;
    }

    public function setIdAlmacen(?Almacenes $id_almacen): self
    {
        $this->id_almacen = $id_almacen;

        return $this;
    }
}
