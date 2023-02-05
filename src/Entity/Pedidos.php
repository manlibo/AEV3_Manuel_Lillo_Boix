<?php

namespace App\Entity;

use App\Repository\PedidosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PedidosRepository::class)]
class Pedidos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'id')]
    private ?int $id = null;

    #[ORM\Column(length: 1)]
    private ?string $tipo = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $observacion = null;

    #[ORM\ManyToOne(targetEntity: Empresas::class, inversedBy: 'pedidos')]
    #[ORM\JoinColumn(name:'id_empresa', nullable: false, referencedColumnName:'id')]
    private ?Empresas $id_empresa = null;

    #[ORM\OneToMany(mappedBy: 'id_pedido', targetEntity: Facturas::class)]
    private Collection $facturas;

    #[ORM\OneToMany(mappedBy: 'id_pedido', targetEntity: Lineaspedidos::class)]
    private Collection $lineaspedidos;

    public function __construct()
    {
        $this->facturas = new ArrayCollection();
        $this->lineaspedidos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
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

    public function getObservacion(): ?string
    {
        return $this->observacion;
    }

    public function setObservacion(?string $observacion): self
    {
        $this->observacion = $observacion;

        return $this;
    }

    public function getIdEmpresa(): ?Empresas
    {
        return $this->id_empresa;
    }

    public function setIdEmpresa(?Empresas $id_empresa): self
    {
        $this->id_empresa = $id_empresa;

        return $this;
    }

    /**
     * @return Collection<int, Facturas>
     */
    public function getFacturas(): Collection
    {
        return $this->facturas;
    }

    public function addFactura(Facturas $factura): self
    {
        if (!$this->facturas->contains($factura)) {
            $this->facturas->add($factura);
            $factura->setIdPedido($this);
        }

        return $this;
    }

    public function removeFactura(Facturas $factura): self
    {
        if ($this->facturas->removeElement($factura)) {
            // set the owning side to null (unless already changed)
            if ($factura->getIdPedido() === $this) {
                $factura->setIdPedido(null);
            }
        }

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
            $lineaspedido->setIdPedido($this);
        }

        return $this;
    }

    public function removeLineaspedido(Lineaspedidos $lineaspedido): self
    {
        if ($this->lineaspedidos->removeElement($lineaspedido)) {
            // set the owning side to null (unless already changed)
            if ($lineaspedido->getIdPedido() === $this) {
                $lineaspedido->setIdPedido(null);
            }
        }

        return $this;
    }
}
