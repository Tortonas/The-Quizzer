<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionAnswerRepository")
 */
class QuestionAnswer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $timeAnswered;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="questionAnswers")
     */
    private ?User $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $username;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question", inversedBy="questionAnswers")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Question $question;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimeAnswered(): ?\DateTimeInterface
    {
        return $this->timeAnswered;
    }

    public function setTimeAnswered(\DateTimeInterface $timeAnswered): self
    {
        $this->timeAnswered = $timeAnswered;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getUserFullName(): ?string
    {
        if ($this->user) {
            return $this->user->getUsername();
        }

        return null;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }
}
