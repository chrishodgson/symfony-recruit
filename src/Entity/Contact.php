<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAtValueTrait;
use App\Entity\Traits\ToArrayTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContactRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Contact
{
    use CreatedAtValueTrait, ToArrayTrait;

    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $landline;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mobile;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkedin;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $role;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $details;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $notifyWhenAvailable = 0;

    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="contacts")
     */
    private $company;

    /**
     * @var Collection|Activity[]
     * @ORM\OneToMany(targetEntity="App\Entity\Activity", mappedBy="contact")
     */
    private $activities;

    /**
     * @var \DateTimeInterface|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $latestActivityAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Contact
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     * @return Contact
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLandline(): ?string
    {
        return $this->landline;
    }

    /**
     * @param null|string $landline
     * @return Contact
     */
    public function setLandline(?string $landline): self
    {
        $this->landline = $landline;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    /**
     * @param null|string $mobile
     * @return Contact
     */
    public function setMobile(?string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    /**
     * @param null|string $linkedin
     * @return Contact
     */
    public function setLinkedin(?string $linkedin): self
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @param null|string $role
     * @return Contact
     */
    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDetails(): ?string
    {
        return $this->details;
    }

    /**
     * @param string $details
     * @return Contact
     */
    public function setDetails(string $details): self
    {
        $this->details = $details;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNotifyWhenAvailable(): bool
    {
        return $this->notifyWhenAvailable;
    }

    /**
     * @param bool $notifyWhenAvailable
     * @return Contact
     */
    public function setNotifyWhenAvailable(bool $notifyWhenAvailable): self
    {
        $this->notifyWhenAvailable = $notifyWhenAvailable;

        return $this;
    }

    /**
     * @return Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    /**
     * @param Company|null $company
     * @return Contact
     */
    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    /**
     * @param Activity $activity
     * @return Contact
     */
    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->setContact($this);
        }

        return $this;
    }

    /**
     * @param Activity $activity
     * @return Contact
     */
    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
            // set the owning side to null (unless already changed)
            if ($activity->getContact() === $this) {
                $activity->setContact(null);
            }
        }

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getLatestActivityAt(): ?\DateTimeInterface
    {
        return $this->latestActivityAt;
    }

    /**
     * @param \DateTimeInterface|null $latestActivityAt
     * @return Contact
     */
    public function setLatestActivityAt(?\DateTimeInterface $latestActivityAt): self
    {
        $this->latestActivityAt = $latestActivityAt;

        return $this;
    }

    /**
     * @return null|\DateTimeInterface
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     * @return Contact
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
