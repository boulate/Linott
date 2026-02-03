<?php

namespace App\Entity;

use App\Repository\PeriodeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PeriodeRepository::class)]
#[ORM\Table(name: 'periode')]
#[ORM\Index(columns: ['user_id', 'date'], name: 'idx_periode_user_date')]
#[ORM\HasLifecycleCallbacks]
class Periode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $heureDebut = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    #[Assert\NotNull]
    #[Assert\GreaterThan(propertyPath: 'heureDebut', message: 'L\'heure de fin doit etre superieure a l\'heure de debut')]
    private ?\DateTimeImmutable $heureFin = null;

    #[ORM\ManyToOne(targetEntity: Section::class, inversedBy: 'periodes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Section $section = null;

    #[ORM\ManyToOne(targetEntity: Axe1::class, inversedBy: 'periodes')]
    private ?Axe1 $axe1 = null;

    #[ORM\ManyToOne(targetEntity: Axe2::class, inversedBy: 'periodes')]
    private ?Axe2 $axe2 = null;

    #[ORM\ManyToOne(targetEntity: Axe3::class, inversedBy: 'periodes')]
    private ?Axe3 $axe3 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column]
    private bool $validee = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

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

    public function isValidee(): bool
    {
        return $this->validee;
    }

    public function setValidee(bool $validee): static
    {
        $this->validee = $validee;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

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
