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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

final class DescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('requirements', TextareaType::class, [
                'constraints' => [
                    new Length(['min' => 100, 'max' => 1024]),
                    new NotContainsEmoji(),
                ],
            ])
            ->add('benefits', TextareaType::class, [
                'constraints' => [
                    new Length(['min' => 100, 'max' => 1024]),
                    new NotContainsEmoji(),
                ],
            ])
        ;
    }
}
