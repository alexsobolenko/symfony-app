<?php

declare(strict_types=1);

namespace App\Model\DTO;

use App\Entity\Author;
use Symfony\Component\Validator\Constraints as Assert;

class AuthorModel
{
    #[Assert\NotBlank(message: 'error.name_is_blank')]
    #[Assert\Length(min: 2, max: 100, minMessage: 'error.name_length_min', maxMessage: 'error.name_length_max')]
    public ?string $name = null;

    /**
     * @param Author $entity
     * @return AuthorModel
     */
    public static function map(Author $entity): AuthorModel
    {
        $model = new self();
        $model->name = $entity->getName();

        return $model;
    }
}
