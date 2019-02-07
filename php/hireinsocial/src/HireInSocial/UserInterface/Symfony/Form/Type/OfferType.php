<?php

declare(strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Form\Type;

use HireInSocial\UserInterface\Symfony\Form\Type\Offer\CompanyType;
use HireInSocial\UserInterface\Symfony\Form\Type\Offer\ContactType;
use HireInSocial\UserInterface\Symfony\Form\Type\Offer\DescriptionType;
use HireInSocial\UserInterface\Symfony\Form\Type\Offer\LocationType;
use HireInSocial\UserInterface\Symfony\Form\Type\Offer\PositionType;
use HireInSocial\UserInterface\Symfony\Form\Type\Offer\SalaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

final class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company', CompanyType::class)
            ->add('position', PositionType::class)
            ->add('salary', SalaryType::class, ['required' => false])
            ->add('contract', ChoiceType::class, [
                'choices' => [
                    'B2B' => 'B2B',
                    'Umowa o Pracę' => 'Umowa o Pracę',
                    'Umowa Zlecenie' => 'Umowa Zlecenie',
                ],
            ])
            ->add('location', LocationType::class)
            ->add('description', DescriptionType::class)
            ->add('contact', ContactType::class)
            ->add('submit', SubmitType::class)
        ;
    }
}
