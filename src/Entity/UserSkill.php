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

    public function getSkill(): ?Skill
    {
        return $this->skills;
    }

    public function setSkill(?Skill $skill): static
    {
        $this->skills = $skill;

        return $this;
    }

    
    public function getLevel(): ?Level
    {
        return $this->levels;
    }
    
    public function setLevel(?Level $level): static
    {
        $this->levels = $level;
        
        return $this;
    }
    public function __toString(): string
    {
        return $this->skills." - ".$this->levels->__toString();
    }

}
