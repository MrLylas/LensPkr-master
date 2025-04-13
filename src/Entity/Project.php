<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $projectName = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    /**
     * @var Collection<int, ProjectImage>
     */
    #[ORM\OneToMany(targetEntity: ProjectImage::class, mappedBy: 'project', orphanRemoval: true)]
    private Collection $projectImages;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    private ?Team $team = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'likedProjects')]
    private Collection $likes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cover = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    private ?Speciality $speciality = null;

    public function __construct()
    {
        $this->projectImages = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectName(): ?string
    {
        return $this->projectName;
    }

    public function setProjectName(string $projectName): static
    {
        $this->projectName = $projectName;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function __toString(): string
    {
        return $this->projectName;
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


    /**
     * @return Collection<int, ProjectImage>
     */
    public function getProjectImages(): Collection
    {
        return $this->projectImages;
    }

    public function addProjectImage(ProjectImage $projectImage): static
    {
        if (!$this->projectImages->contains($projectImage)) {
            $this->projectImages->add($projectImage);
            $projectImage->setProject($this);
        }

        return $this;
    }

    public function removeProjectImage(ProjectImage $projectImage): static
    {
        if ($this->projectImages->removeElement($projectImage)) {
            // set the owning side to null (unless already changed)
            if ($projectImage->getProject() === $this) {
                $projectImage->setProject(null);
            }
        }

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): static
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(User $user): static
    {
        if (!$this->likes->contains($user)) {
            $this->likes->add($user);
            $user->addLikedProject($this); // Mise à jour côté User
        }
        return $this;
    }
    
    public function removeLike(User $user): static
    {
        if ($this->likes->removeElement($user)) {
            $user->removeLikedProject($this); // Mise à jour côté User
        }
        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): static
    {
        $this->cover = $cover;

        return $this;
    }

    public function getSpeciality(): ?Speciality
    {
        return $this->speciality;
    }

    public function setSpeciality(?Speciality $speciality): static
    {
        $this->speciality = $speciality;

        return $this;
    }
}
