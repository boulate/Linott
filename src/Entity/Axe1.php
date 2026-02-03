<?php

namespace App\Entity;

use App\Repository\Axe1Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: Axe1Repository::class)]
#[ORM\Table(name: 'axe1')]
class Axe1
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

    #[ORM\ManyToOne(targetEntity: Section::class, inversedBy: 'axes1')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Section $section = null;

    #[ORM\Column]
    private bool $actif = true;

    #[ORM\Column]
    private int $ordre = 0;

    /**
     * @var Collection<int, Axe2>
     */
    #[ORM\OneToMany(targetEntity: Axe2::class, mappedBy: 'axe1', orphanRemoval: true)]
    #[ORM\OrderBy(['ordre' => 'ASC', 'libelle' => 'ASC'])]
    private Collection $axes2;

    /**
     * @var Collection<int, Periode>
     */
    #[ORM\OneToMany(targetEntity: Periode::class, mappedBy: 'axe1')]
    private Collection $periodes;

    public function __construct()
    {
        $this->axes2 = new ArrayCollection();
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

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): static
    {
        $this->section = $section;

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
     * @return Collection<int, Axe2>
     */
    public function getAxes2(): Collection
    {
        return $this->axes2;
    }

    public function addAxes2(Axe2 $axe2): static
    {
        if (!$this->axes2->contains($axe2)) {
            $this->axes2->add($axe2);
            $axe2->setAxe1($this);
        }

        return $this;
    }

    public function removeAxes2(Axe2 $axe2): static
    {
        if ($this->axes2->removeElement($axe2)) {
            if ($axe2->getAxe1() === $this) {
                $axe2->setAxe1(null);
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
