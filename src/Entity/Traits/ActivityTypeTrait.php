<?php

namespace App\Entity\Traits;

trait ActivityTypeTrait
{
    final public function getActivityTypes(): array
    {
        return [
            1 => 'Email',
            2 => 'Phone',
            3 => 'Voice Mail',
            4 => 'Left Message'
        ];
    }

    final public function getActivityTypeKeys(): array
    {
        return array_keys($this->getActivityTypes());
    }

    final public function getActivityTypeLabel($id): ?string
    {
        return $this->getActivityTypes()[$id] ?? null;
    }
}