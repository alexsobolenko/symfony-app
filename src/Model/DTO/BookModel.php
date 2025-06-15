<?php

declare(strict_types=1);

namespace App\Model\DTO;

use App\Entity\Book;
use Symfony\Component\Validator\Constraints as Assert;

class BookModel
{
    #[Assert\NotBlank(message: 'error.name_is_blank')]
    #[Assert\Length(min: 2, max: 100, minMessage: 'error.name_length_min', maxMessage: 'error.name_length_max')]
    public ?string $name = null;

    #[Assert\NotBlank(message: 'error.price_is_blank')]
    #[Assert\Range(notInRangeMessage: 'error.price_not_in_range', min: 0.1, max: 1000000)]
    public ?float $price = null;

    #[Assert\NotBlank(message: 'error.author_is_blank')]
    public ?int $author = null;

    /**
     * @param Book $entity
     * @return BookModel
     */
    public static function map(Book $entity): BookModel
    {
        $model = new self();
        $model->name = $entity->getName();
        $model->price = $entity->getPrice();
        $model->author = $entity->getAuthor()->getId();

        return $model;
    }
}
