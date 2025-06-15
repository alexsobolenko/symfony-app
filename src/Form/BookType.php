<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\DTO\BookModel;
use App\Repository\AuthorRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BookType extends AbstractType
{
    /**
     * @param TranslatorInterface $translator
     * @param AuthorRepository $authorRepository
     */
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly AuthorRepository $authorRepository
    ) {}

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws \RuntimeException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$this->authorRepository->hasAuthors()) {
            throw new \RuntimeException('error.has_no_authors');
        }

        $authors = [];
        foreach ($this->authorRepository->findBy([], ['name' => 'ASC']) as $author) {
            $authors[$author->getName()] = $author->getId();
        }

        $builder
            ->add('author', ChoiceType::class, [
                'required' => true,
                'label' => $this->translator->trans('page.book.details.author'),
                'choices' => $authors,
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'label' => $this->translator->trans('page.book.details.name'),
            ])
            ->add('price', NumberType::class, [
                'required' => true,
                'label' => $this->translator->trans('page.book.details.price'),
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans('main.save'),
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookModel::class,
        ]);
    }
}
