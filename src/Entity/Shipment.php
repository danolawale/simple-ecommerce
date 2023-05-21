<?php

namespace App\Entity;

use App\Repository\ShipmentRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShipmentRepository::class)]
class Shipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $shipmentRef = null;

    #[ORM\OneToOne(inversedBy: 'shipment', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $labelUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $trackingCode = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShipmentRef(): ?string
    {
        return $this->shipmentRef;
    }

    public function setShipmentRef(string $shipmentRef): self
    {
        $this->shipmentRef = $shipmentRef;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLabelUrl(): ?string
    {
        return $this->labelUrl;
    }

    public function setLabelUrl(?string $labelUrl): self
    {
        $this->labelUrl = $labelUrl;

        return $this;
    }

    public function getTrackingCode(): ?string
    {
        return $this->trackingCode;
    }

    public function setTrackingCode(?string $trackingCode): self
    {
        $this->trackingCode = $trackingCode;

        return $this;
    }
}
