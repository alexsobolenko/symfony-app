<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\DTO\AuthorModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AuthorType extends AbstractType
{
    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {}

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => $this->translator->trans('page.author.details.name'),
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
            'data_class' => AuthorModel::class,
        ]);
    }
}
