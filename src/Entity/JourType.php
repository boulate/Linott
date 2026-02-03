<?php

namespace App\Entity;

use App\Repository\JourTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: JourTypeRepository::class)]
#[ORM\Table(name: 'jour_type')]
#[ORM\HasLifecycleCallbacks]
class JourType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column]
    private bool $partage = false;

    #[ORM\Column]
    private bool $actif = true;

    #[ORM\Column]
    private int $ordre = 0;

    /**
     * @var Collection<int, JourTypePeriode>
     */
    #[ORM\OneToMany(targetEntity: JourTypePeriode::class, mappedBy: 'jourType', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['ordre' => 'ASC', 'heureDebut' => 'ASC'])]
    private Collection $periodes;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->periodes = new ArrayCollection();
    }

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
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

    public function isPartage(): bool
    {
        return $this->partage;
    }

    public function setPartage(bool $partage): static
    {
        $this->partage = $partage;

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
     * @return Collection<int, JourTypePeriode>
     */
    public function getPeriodes(): Collection
    {
        return $this->periodes;
    }

    public function addPeriode(JourTypePeriode $periode): static
    {
        if (!$this->periodes->contains($periode)) {
            $this->periodes->add($periode);
            $periode->setJourType($this);
        }

        return $this;
    }

    public function removePeriode(JourTypePeriode $periode): static
    {
        if ($this->periodes->removeElement($periode)) {
            if ($periode->getJourType() === $this) {
                $periode->setJourType(null);
            }
        }

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

    public function isGlobal(): bool
    {
        return $this->user === null && $this->partage === true;
    }

    public function isPersonnel(): bool
    {
        return $this->user !== null;
    }

    public function __toString(): string
    {
        return $this->nom ?? '';
    }

    public function getTotalMinutes(): int
    {
        $total = 0;
        foreach ($this->periodes as $periode) {
            $total += $periode->getDureeMinutes();
        }

        return $total;
    }

    public function getTotalFormatted(): string
    {
        $minutes = $this->getTotalMinutes();
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return sprintf('%d:%02d', $hours, $mins);
    }
}
