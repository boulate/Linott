<?php

namespace App\Entity;

use App\Repository\JourTypePeriodeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: JourTypePeriodeRepository::class)]
#[ORM\Table(name: 'jour_type_periode')]
class JourTypePeriode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: JourType::class, inversedBy: 'periodes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?JourType $jourType = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $heureDebut = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    #[Assert\NotNull]
    #[Assert\GreaterThan(propertyPath: 'heureDebut', message: 'L\'heure de fin doit etre superieure a l\'heure de debut')]
    private ?\DateTimeImmutable $heureFin = null;

    #[ORM\ManyToOne(targetEntity: Section::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Section $section = null;

    #[ORM\ManyToOne(targetEntity: Axe1::class)]
    private ?Axe1 $axe1 = null;

    #[ORM\ManyToOne(targetEntity: Axe2::class)]
    private ?Axe2 $axe2 = null;

    #[ORM\ManyToOne(targetEntity: Axe3::class)]
    private ?Axe3 $axe3 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column]
    private int $ordre = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJourType(): ?JourType
    {
        return $this->jourType;
    }

    public function setJourType(?JourType $jourType): static
    {
        $this->jourType = $jourType;

        return $this;
    }

    public function getHeureDebut(): ?\DateTimeImmutable
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(\DateTimeImmutable $heureDebut): static
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getHeureFin(): ?\DateTimeImmutable
    {
        return $this->heureFin;
    }

    public function setHeureFin(\DateTimeImmutable $heureFin): static
    {
        $this->heureFin = $heureFin;

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

    public function getAxe1(): ?Axe1
    {
        return $this->axe1;
    }

    public function setAxe1(?Axe1 $axe1): static
    {
        $this->axe1 = $axe1;

        return $this;
    }

    public function getAxe2(): ?Axe2
    {
        return $this->axe2;
    }

    public function setAxe2(?Axe2 $axe2): static
    {
        $this->axe2 = $axe2;

        return $this;
    }

    public function getAxe3(): ?Axe3
    {
        return $this->axe3;
    }

    public function setAxe3(?Axe3 $axe3): static
    {
        $this->axe3 = $axe3;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

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

    public function getDureeMinutes(): int
    {
        if (!$this->heureDebut || !$this->heureFin) {
            return 0;
        }

        $debut = $this->heureDebut->getTimestamp();
        $fin = $this->heureFin->getTimestamp();

        return (int) (($fin - $debut) / 60);
    }

    public function getDureeFormatted(): string
    {
        $minutes = $this->getDureeMinutes();
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return sprintf('%d:%02d', $hours, $mins);
    }
}
