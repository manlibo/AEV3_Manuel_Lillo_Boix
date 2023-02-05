<?php

namespace App\Entity;

use App\Repository\FacturasRepository;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacturasRepository::class)]
class Facturas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'id')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $tipo = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $valor = null;

    #[ORM\ManyToOne(targetEntity: Pedidos::class, inversedBy: 'facturas')]
    #[ORM\JoinColumn(name:'id_pedido', nullable: false, referencedColumnName:'id')]
    private ?Pedidos $id_pedido = null;

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

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getValor(): ?string
    {
        return $this->valor;
    }

    public function setValor(string $valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getIdPedido(): ?Pedidos
    {
        return $this->id_pedido;
    }

    public function setIdPedido(?Pedidos $id_pedido): self
    {
        $this->id_pedido = $id_pedido;

        return $this;
    }
}
