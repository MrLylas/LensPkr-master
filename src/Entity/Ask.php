<?php

namespace App\Entity;

use App\Repository\AskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AskRepository::class)]
class Ask
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_ask = null;

    #[ORM\ManyToOne(inversedBy: 'asks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'asks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Job $job = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAsk(): ?\DateTimeInterface
    {
        return $this->date_ask;
    }

    public function setDateAsk(\DateTimeInterface $date_ask): static
    {
        $this->date_ask = $date_ask;

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

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): static
    {
        $this->job = $job;

        return $this;
    }
}
