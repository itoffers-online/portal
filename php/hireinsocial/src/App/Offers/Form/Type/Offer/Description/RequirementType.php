<?php

declare(strict_types=1);

/*
 * This file is part of the Hire in Social project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Offers\Form\Type\Offer\Description;

use App\Offers\Validator\Constraints\NotContainsEmoji;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class RequirementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('skill', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['min' => 1, 'max' => 64]),
                    new NotContainsEmoji(),
                ],
            ])
            ->add(
                'required',
                ChoiceType::class,
                [
                'choices' => [
                    'Required'  => 1,
                    'Nice to have'  => 0,
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]
            )
            ->add(
                'experience',
                ChoiceType::class,
                [
                'required' => false,
                'choices' => [
                    '1+'  => 1,
                    '2+'  => 2,
                    '3+'  => 3,
                    '4+'  => 4,
                    '5+'  => 5,
                    '6+'  => 6,
                    '7+'  => 7,
                    '8+'  => 8,
                    '9+'  => 9,
                    '10+'  => 10,
                ],
            ]
            );
    }
}
