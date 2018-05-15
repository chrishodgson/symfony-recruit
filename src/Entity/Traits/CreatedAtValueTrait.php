<?php

namespace App\Entity\Traits;

trait CreatedAtValueTrait
{
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if (!$this->getCreatedAt()) {
            $this->createdAt = new \DateTime();
        }
    }
}