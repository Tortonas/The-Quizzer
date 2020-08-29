<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private ?string $question = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $answer = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $timeModified = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\QuestionAnswer", mappedBy="question", orphanRemoval=true)
     */
    private $questionAnswers = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $active = null;

    public function __construct()
    {
        $this->questionAnswers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getTimeModified(): ?\DateTimeInterface
    {
        return $this->timeModified;
    }

    public function setTimeModified(\DateTimeInterface $timeModified): self
    {
        $this->timeModified = $timeModified;

        return $this;
    }

    /**
     * @return Collection|QuestionAnswer[]
     */
    public function getQuestionAnswers(): Collection
    {
        return $this->questionAnswers;
    }

    public function addQuestionAnswer(QuestionAnswer $questionAnswer): self
    {
        if (!$this->questionAnswers->contains($questionAnswer)) {
            $this->questionAnswers[] = $questionAnswer;
            $questionAnswer->setQuestion($this);
        }

        return $this;
    }

    public function removeQuestionAnswer(QuestionAnswer $questionAnswer): self
    {
        if ($this->questionAnswers->contains($questionAnswer)) {
            $this->questionAnswers->removeElement($questionAnswer);
            // set the owning side to null (unless already changed)
            if ($questionAnswer->getQuestion() === $this) {
                $questionAnswer->setQuestion(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
    }

    public function __toString()
    {
        return $this->question;
    }
}
