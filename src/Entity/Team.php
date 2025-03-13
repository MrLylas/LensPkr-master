<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'teams')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $team_pic = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $team_banner = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'followedTeams')]
    #[ORM\JoinTable(name: 'team_user')] 
    private Collection $follow;

    public function __construct()
    {
        $this->follow = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): static
    {
        $this->creator = $creator;

        return $this;
    }

    public function getTeamPic(): ?string
    {
        return $this->team_pic;
    }

    public function setTeamPic(?string $team_pic): static
    {
        $this->team_pic = $team_pic;

        return $this;
    }

    public function getTeamBanner(): ?string
    {
        return $this->team_banner;
    }

    public function setTeamBanner(?string $team_banner): static
    {
        $this->team_banner = $team_banner;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getFollow(): Collection
    {
        return $this->follow;
    }

// src/Entity/Team.php

public function addFollow(User $follow): static
{
    if (!$this->follow->contains($follow)) {
        $this->follow->add($follow);
        $follow->addFollowedTeam($this); // Assurez-vous que la relation est bien bidirectionnelle
    }

    return $this;
}

public function removeFollow(User $follow): static
{
    if ($this->follow->removeElement($follow)) {
        $follow->removeFollowedTeam($this); // Retirer l'équipe de la liste des équipes suivies par l'utilisateur
    }

    return $this;
}

}
