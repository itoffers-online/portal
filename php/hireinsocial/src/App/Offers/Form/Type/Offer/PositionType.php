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

namespace App\Offers\Form\Type\Offer;

use App\Offers\Validator\Constraints\NotContainsEmoji;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

final class PositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('seniorityLevel', ChoiceType::class, [
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Intern'  => 0,
                    'Junior'  => 1,
                    'Mid'  => 2,
                    'Senior'  => 3,
                    'Expert'  => 4,
                ],
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length(['min' => 3, 'max' => 255]),
                    new NotContainsEmoji(),
                ],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new Length(['min' => 50, 'max' => 1024]),
                    new NotContainsEmoji(),
                ],
            ])
        ;
    }
}
