<?php

declare(strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Form\Type;

use HireInSocial\Application\Query\Offer\OfferFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OfferFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');

        $builder
            ->add('remote', CheckboxType::class, [
                'required' => false,
            ])
            ->add('with_salary', CheckboxType::class, [
                'required' => false,
            ])
            ->add('sort_by', ChoiceType::class, [
                'choices' => [
                    '' => null,
                    'Salary ascending' =>  OfferFilter::SORT_SALARY_ASC,
                    'Salary descending' => OfferFilter::SORT_SALARY_DESC,
                    'Added at ascending' => OfferFilter::SORT_CREATED_AT_ASC,
                    'Added at descending' => OfferFilter::SORT_CREATED_AT_DESC,
                ],
                'choice_value' => function (string $value = null) {
                    return $value;
                },
                'required' => false,
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
