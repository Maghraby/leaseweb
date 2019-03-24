<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServerRepository")
 * @UniqueEntity("asset_id")
 */
class Server
{
    use TimestampableTrait;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", unique=true)
     * @Assert\NotBlank
     * @Assert\GreaterThan(
     *    value = 0
     * )
     */
    private $asset_id;

    /**
     * @ORM\Column(type="string", length=40)
     * @Assert\NotBlank
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank
     * @Assert\GreaterThan(
     *     value = 0.0
     * )
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ram", mappedBy="server", orphanRemoval=true)
     */
    private $rams;

    public function __construct()
    {
        $this->rams = new ArrayCollection();
        $this->updateTimestamps();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAssetId(): ?int
    {
        return $this->asset_id;
    }

    public function setAssetId(int $asset_id): self
    {
        $this->asset_id = $asset_id;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|Ram[]
     */
    public function getRams(): Collection
    {
        return $this->rams;
    }

    public function addRam(Ram $ram): self
    {
        if (!$this->rams->contains($ram)) {
            $this->rams[] = $ram;
            $ram->setServer($this);
        }

        return $this;
    }

    public function addRams($rams): self
    {
        foreach ($rams as $ram) {
            $this->addRam($ram);
        }

        return $this;
    }

    public function removeRam(Ram $ram): self
    {
        if ($this->rams->contains($ram)) {
            $this->rams->removeElement($ram);
            // set the owning side to null (unless already changed)
            if ($ram->getServer() === $this) {
                $ram->setServer(null);
            }
        }

        return $this;
    }
}
