<?php

namespace App\Entity\Traits;

trait CompanyTypeTrait
{
    final public function getCompanyTypes(): array
    {
        return [
            1 => 'Recruitment',
            2 => 'Corporate',
            3 => 'Digital'
        ];
    }

    final public function getCompanyTypeLabel($id): ?string
    {
        return $this->getCompanyTypes()[$id] ?? null;
    }
}