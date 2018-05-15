<?php

namespace App\Entity;

use App\Entity\Traits\ActivityTypeTrait;
use App\Entity\Traits\CreatedAtValueTrait;
use App\Repository\ContactRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Activity
{
    use ActivityTypeTrait, CreatedAtValueTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $summary;

    /**
     * @var null|string
     * @ORM\Column(type="text", nullable=true)
     */
    private $transcript;

    /**
     * @var Contact
     * @ORM\ManyToOne(targetEntity="App\Entity\Contact", inversedBy="activities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contact;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return Activity
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param $type
     * @return Activity
     */
    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @param $summary
     * @return Activity
     */
    public function setSummary($summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTranscript(): ?string
    {
        return $this->transcript;
    }

    /**
     * @param $transcript
     * @return Activity
     */
    public function setTranscript($transcript): self
    {
        $this->transcript = $transcript;

        return $this;
    }

    /**
     * @return null|Contact
     */
    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    /**
     * @param $contact
     * @return Activity
     */
    public function setContact($contact): self
    {
        $this->contact = $contact;

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
     * @param $createdAt
     * @return Activity
     */
    public function setCreatedAt($createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @ORM\PostPersist()
     * @throws \Doctrine\ORM\ORMException
     */
    public function setLatestActivityOnContact(LifecycleEventArgs $args)
    {
        $existing = $this->getContact()->getLatestActivityAt();
        if ($existing && $existing->getTimestamp() > $this->getCreatedAt()->getTimestamp()) {
            return;
        }
        $this->getContact()->setLatestActivityAt($this->getCreatedAt());
        $args->getEntityManager()->persist($this->getContact());
        $args->getEntityManager()->flush();
    }

    /**
     * @ORM\PostRemove()
     * @throws \Doctrine\ORM\ORMException
     */
    public function resetLatestActivityOnContact(LifecycleEventArgs $args)
    {
        /** @var ContactRepository $repository */
        $repository = $args->getEntityManager()->getRepository(Contact::class);
        $result = $repository->calculateLatestActivityAt($this->getContact());
        $this->getContact()->setLatestActivityAt($result['createdAt'] ?? null);
        $args->getEntityManager()->persist($this->getContact());
        $args->getEntityManager()->flush();
    }
}
