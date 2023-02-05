<?php

namespace App\Entity;

use App\Repository\ProductosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductosRepository::class)]
class Productos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'id')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 4)]
    private ?string $unidad = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $clasificacion = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $preciounidad = null;

    #[ORM\OneToMany(mappedBy: 'id_producto', targetEntity: Lineaspedidos::class)]
    private Collection $lineaspedidos;

    #[ORM\ManyToOne(targetEntity: Almacenes::class, inversedBy: 'productos')]
    #[ORM\JoinColumn(name:'id_almacen', nullable: false, referencedColumnName:'id')]
    private ?Almacenes $id_almacen = null;

    #[ORM\OneToMany(mappedBy: 'id_producto', targetEntity: Stock::class)]
    private Collection $stocks;

    public function __construct()
    {
        $this->lineaspedidos = new ArrayCollection();
        $this->stocks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

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

    public function getClasificacion(): ?string
    {
        return $this->clasificacion;
    }

    public function setClasificacion(?string $clasificacion): self
    {
        $this->clasificacion = $clasificacion;

        return $this;
    }

    public function getPreciounidad(): ?string
    {
        return $this->preciounidad;
    }

    public function setPreciounidad(string $preciounidad): self
    {
        $this->preciounidad = $preciounidad;

        return $this;
    }

    /**
     * @return Collection<int, Lineaspedidos>
     */
    public function getLineaspedidos(): Collection
    {
        return $this->lineaspedidos;
    }

    public function addLineaspedido(Lineaspedidos $lineaspedido): self
    {
        if (!$this->lineaspedidos->contains($lineaspedido)) {
            $this->lineaspedidos->add($lineaspedido);
            $lineaspedido->setIdProducto($this);
        }

        return $this;
    }

    public function removeLineaspedido(Lineaspedidos $lineaspedido): self
    {
        if ($this->lineaspedidos->removeElement($lineaspedido)) {
            // set the owning side to null (unless already changed)
            if ($lineaspedido->getIdProducto() === $this) {
                $lineaspedido->setIdProducto(null);
            }
        }

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

    /**
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(Stock $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setIdProducto($this);
        }

        return $this;
    }

    public function removeStock(Stock $stock): self
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getIdProducto() === $this) {
                $stock->setIdProducto(null);
            }
        }

        return $this;
    }
}
