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

namespace App\Form\Type\Offer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length(['min' => 3, 'max' => 255]),
                ],
            ])
            ->add('email', TextType::class, [
                'constraints' => [
                    new Email(),
                    new NotBlank(),
                ],
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['max' => 16]),
                ],
            ])
        ;
    }
}
