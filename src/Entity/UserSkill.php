<?php

namespace App\Entity;

use App\Repository\UserSkillRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSkillRepository::class)]
class UserSkill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userSkills')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userSkills')]
    private ?Skill $skills = null;

    #[ORM\ManyToOne(inversedBy: 'userSkills')]
    private ?Level $levels = null;

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

    public function getSkills(): ?Skill
    {
        return $this->skills;
    }

    public function setSkills(?Skill $skills): static
    {
        $this->skills = $skills;

        return $this;
    }

    
    public function getLevels(): ?Level
    {
        return $this->levels;
    }
    
    public function setLevels(?Level $levels): static
    {
        $this->levels = $levels;
        
        return $this;
    }
    public function __toString(): string
    {
        return $this->skills." - ".$this->levels->__toString();
    }

}
