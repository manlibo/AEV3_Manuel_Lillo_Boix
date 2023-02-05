<?php

namespace App\Entity;

use App\Repository\AlmacenesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlmacenesRepository::class)]
class Almacenes
{
    #[ORM\Id]
    #[ORM\Column(name:'id')]
    private ?int $id = null;

    #[ORM\Column(length: 5)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localizacion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\OneToMany(mappedBy: 'id_almacen', targetEntity: Productos::class)]
    private Collection $productos;

    #[ORM\OneToMany(mappedBy: 'id_almacen', targetEntity: Stock::class)]
    private Collection $stocks;

    public function __construct()
    {
        $this->productos = new ArrayCollection();
        $this->stocks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

     /**
     * Set the value of id
     *
     * @param  ?int  $id
     *
     * @return  self
     */
    public function setId(?int $id)
    {
        $this->id = $id;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getLocalizacion(): ?string
    {
        return $this->localizacion;
    }

    public function setLocalizacion(?string $localizacion): self
    {
        $this->localizacion = $localizacion;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * @return Collection<int, Productos>
     */
    public function getProductos(): Collection
    {
        return $this->productos;
    }

    public function addProducto(Productos $producto): self
    {
        if (!$this->productos->contains($producto)) {
            $this->productos->add($producto);
            $producto->setIdAlmacen($this);
        }

        return $this;
    }

    public function removeProducto(Productos $producto): self
    {
        if ($this->productos->removeElement($producto)) {
            // set the owning side to null (unless already changed)
            if ($producto->getIdAlmacen() === $this) {
                $producto->setIdAlmacen(null);
            }
        }

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
            $stock->setIdAlmacen($this);
        }

        return $this;
    }

    public function removeStock(Stock $stock): self
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getIdAlmacen() === $this) {
                $stock->setIdAlmacen(null);
            }
        }

        return $this;
    }

   
}
