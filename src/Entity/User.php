<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $username;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\Column(type="date")
     */
    private \DateTimeInterface $registerAt;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\QuestionAnswer", mappedBy="user")
     */
    private $questionAnswers;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastVisit = null;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default" : 1})
     */
    private bool $emailSubscription = true;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $lastTimeGotEmail = null;

    public function __construct()
    {
        $this->questionAnswers = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getRegisterAt()
    {
        return $this->registerAt;
    }

    /**
     * @param mixed $registerAt
     */
    public function setRegisterAt($registerAt): void
    {
        $this->registerAt = $registerAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
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
            $questionAnswer->setUser($this);
        }

        return $this;
    }

    public function removeQuestionAnswer(QuestionAnswer $questionAnswer): self
    {
        if ($this->questionAnswers->contains($questionAnswer)) {
            $this->questionAnswers->removeElement($questionAnswer);
            // set the owning side to null (unless already changed)
            if ($questionAnswer->getUser() === $this) {
                $questionAnswer->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastVisit()
    {
        return $this->lastVisit;
    }

    /**
     * @param mixed $lastVisit
     */
    public function setLastVisit($lastVisit): void
    {
        $this->lastVisit = $lastVisit;
    }

    /**
     * @return bool
     */
    public function getEmailSubscription(): bool
    {
        return $this->emailSubscription;
    }

    /**
     * @param bool $emailSubscription
     */
    public function setEmailSubscription(bool $emailSubscription): void
    {
        $this->emailSubscription = $emailSubscription;
    }

    /**
     * @return \DateTime
     */
    public function getLastTimeGotEmail()
    {
        return $this->lastTimeGotEmail;
    }

    /**
     * @param \DateTime $lastTimeGotEmail
     */
    public function setLastTimeGotEmail($lastTimeGotEmail): void
    {
        $this->lastTimeGotEmail = $lastTimeGotEmail;
    }
}
