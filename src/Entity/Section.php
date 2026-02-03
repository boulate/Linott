<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SectionRepository::class)]
#[ORM\Table(name: 'section')]
class Section
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $libelle = null;

    #[ORM\Column]
    private bool $actif = true;

    #[ORM\Column]
    private int $ordre = 0;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $couleur = null;

    public const COULEURS = [
        'gray' => ['label' => 'Gris', 'bg' => '#F3F4F6', 'text' => '#374151', 'border' => '#D1D5DB'],
        'brown' => ['label' => 'Marron', 'bg' => '#FEF3C7', 'text' => '#92400E', 'border' => '#D97706'],
        'orange' => ['label' => 'Orange', 'bg' => '#FFEDD5', 'text' => '#C2410C', 'border' => '#FB923C'],
        'yellow' => ['label' => 'Jaune', 'bg' => '#FEF9C3', 'text' => '#A16207', 'border' => '#FACC15'],
        'green' => ['label' => 'Vert', 'bg' => '#DCFCE7', 'text' => '#166534', 'border' => '#4ADE80'],
        'blue' => ['label' => 'Bleu', 'bg' => '#DBEAFE', 'text' => '#1E40AF', 'border' => '#60A5FA'],
        'purple' => ['label' => 'Violet', 'bg' => '#F3E8FF', 'text' => '#7C3AED', 'border' => '#A78BFA'],
        'pink' => ['label' => 'Rose', 'bg' => '#FCE7F3', 'text' => '#BE185D', 'border' => '#F472B6'],
        'red' => ['label' => 'Rouge', 'bg' => '#FEE2E2', 'text' => '#B91C1C', 'border' => '#F87171'],
        'teal' => ['label' => 'Turquoise', 'bg' => '#CCFBF1', 'text' => '#0F766E', 'border' => '#2DD4BF'],
    ];

    /**
     * @var Collection<int, Axe1>
     */
    #[ORM\OneToMany(targetEntity: Axe1::class, mappedBy: 'section', orphanRemoval: true)]
    #[ORM\OrderBy(['ordre' => 'ASC', 'libelle' => 'ASC'])]
    private Collection $axes1;

    /**
     * @var Collection<int, Periode>
     */
    #[ORM\OneToMany(targetEntity: Periode::class, mappedBy: 'section')]
    private Collection $periodes;

    public function __construct()
    {
        $this->axes1 = new ArrayCollection();
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
     * @return Collection<int, Axe1>
     */
    public function getAxes1(): Collection
    {
        return $this->axes1;
    }

    public function addAxes1(Axe1 $axe1): static
    {
        if (!$this->axes1->contains($axe1)) {
            $this->axes1->add($axe1);
            $axe1->setSection($this);
        }

        return $this;
    }

    public function removeAxes1(Axe1 $axe1): static
    {
        if ($this->axes1->removeElement($axe1)) {
            if ($axe1->getSection() === $this) {
                $axe1->setSection(null);
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

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getCouleurConfig(): array
    {
        if ($this->couleur && isset(self::COULEURS[$this->couleur])) {
            return self::COULEURS[$this->couleur];
        }

        return self::COULEURS['gray'];
    }

    public function getCouleurStyle(): string
    {
        $config = $this->getCouleurConfig();

        return sprintf(
            'background-color: %s; color: %s; border-color: %s;',
            $config['bg'],
            $config['text'],
            $config['border']
        );
    }
}
