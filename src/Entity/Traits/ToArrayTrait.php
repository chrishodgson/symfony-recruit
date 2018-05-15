<?php

namespace App\Entity\Traits;

trait ToArrayTrait
{
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}