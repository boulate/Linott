<?php

namespace App\Entity;

use App\Repository\Axe2Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: Axe2Repository::class)]
#[ORM\Table(name: 'axe2')]
class Axe2
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(targetEntity: Axe1::class, inversedBy: 'axes2')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Axe1 $axe1 = null;

    #[ORM\Column]
    private bool $actif = true;

    #[ORM\Column]
    private int $ordre = 0;

    /**
     * @var Collection<int, Axe3>
     */
    #[ORM\OneToMany(targetEntity: Axe3::class, mappedBy: 'axe2', orphanRemoval: true)]
    #[ORM\OrderBy(['ordre' => 'ASC', 'libelle' => 'ASC'])]
    private Collection $axes3;

    /**
     * @var Collection<int, Periode>
     */
    #[ORM\OneToMany(targetEntity: Periode::class, mappedBy: 'axe2')]
    private Collection $periodes;

    public function __construct()
    {
        $this->axes3 = new ArrayCollection();
        $this->periodes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getAxe1(): ?Axe1
    {
        return $this->axe1;
    }

    public function setAxe1(?Axe1 $axe1): static
    {
        $this->axe1 = $axe1;

        return $this;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getOrdre(): int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): static
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * @return Collection<int, Axe3>
     */
    public function getAxes3(): Collection
    {
        return $this->axes3;
    }

    public function addAxes3(Axe3 $axe3): static
    {
        if (!$this->axes3->contains($axe3)) {
            $this->axes3->add($axe3);
            $axe3->setAxe2($this);
        }

        return $this;
    }

    public function removeAxes3(Axe3 $axe3): static
    {
        if ($this->axes3->removeElement($axe3)) {
            if ($axe3->getAxe2() === $this) {
                $axe3->setAxe2(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Periode>
     */
    public function getPeriodes(): Collection
    {
        return $this->periodes;
    }

    public function __toString(): string
    {
        return $this->code . ' - ' . $this->libelle;
    }
}
