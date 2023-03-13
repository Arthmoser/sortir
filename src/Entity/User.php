<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['nickname'], message: 'These nickname already exists! Please choose another nickname!')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(
        message: "Un email est requis pour ce champ")]
    #[Assert\Email(
        message: 'Le mail {{ value }} ne fonctionne pas')]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]

//    #[Assert\Length(
//        min: 8,
//        max: 25,
//        minMessage: "Minimum {{ limit }} caractères !",
//        maxMessage: "Maximum {{ limit }} caractères !"
//    )]
    private ?string $password = null;


    #[Assert\NotBlank(
        message: "Un pseudo est requis pour ce champ")]
    #[Assert\Length(
        min: 8,
        max: 25,
        minMessage: "Minimum {{ limit }} caractères !",
        maxMessage: "Maximum {{ limit }} caractères !"
    )]
    #[ORM\Column(length: 50, unique: true)]
    private ?string $nickname = null;


    #[Assert\NotBlank(
        message: "Ce champ ne peut pas être vide")]
    #[ORM\Column(length: 50)]
    private ?string $lastname = null;

    #[Assert\NotBlank(
        message: "Ce champ ne peut pas être vide")]
    #[ORM\Column(length: 50)]
    private ?string $firstname = null;


    #[Assert\NotBlank(
        message: "Ce champ ne peut pas être vide")]
    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?bool $isAllowed = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Activity::class)]
    private Collection $myActivities;

    #[ORM\ManyToMany(targetEntity: Activity::class, inversedBy: 'users')]
    private Collection $activities;

    #[ORM\Column(length: 50, nullable: false)]
    private ?string $profilePicture = null;

    public function __construct()
    {
        $this->myActivities = new ArrayCollection();
        $this->activities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function isIsAllowed(): ?bool
    {
        return $this->isAllowed;
    }

    public function setIsAllowed(bool $isAllowed): self
    {
        $this->isAllowed = $isAllowed;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getMyActivities(): Collection
    {
        return $this->myActivities;
    }

    public function addMyActivity(Activity $myActivity): self
    {
        if (!$this->myActivities->contains($myActivity)) {
            $this->myActivities->add($myActivity);
            $myActivity->setUser($this);
        }

        return $this;
    }

    public function removeMyActivity(Activity $myActivity): self
    {
        if ($this->myActivities->removeElement($myActivity)) {
            // set the owning side to null (unless already changed)
            if ($myActivity->getUser() === $this) {
                $myActivity->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        $this->activities->removeElement($activity);

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    #[ORM\PrePersist]
    public function setProfilePictureAtValue(): void
    {
        $this->setProfilePicture('profilePicture.png');
        $this->setIsAllowed(true);
    }

}
