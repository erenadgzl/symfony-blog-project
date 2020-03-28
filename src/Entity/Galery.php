<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GaleryRepository")
 */
class Galery
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $part;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $slider;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPart(): ?int
    {
        return $this->part;
    }

    public function setPart(?int $part): self
    {
        $this->part = $part;

        return $this;
    }

    public function getSlider(): ?bool
    {
        return $this->slider;
    }

    public function setSlider(?bool $slider): self
    {
        $this->slider = $slider;

        return $this;
    }
}
