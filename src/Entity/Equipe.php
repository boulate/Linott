<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
#[ORM\Table(name: 'equipe')]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private bool $actif = true;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $couleur = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'equipes')]
    #[ORM\JoinTable(name: 'equipe_user')]
    #[ORM\OrderBy(['nom' => 'ASC', 'prenom' => 'ASC'])]
    private Collection $users;

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

    public function __construct()
    {
        $this->users = new ArrayCollection();
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

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function __toString(): string
    {
        return $this->code . ' - ' . $this->nom;
    }
}
