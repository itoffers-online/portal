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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

final class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('remote', CheckboxType::class, [
                'required' => false,
            ])
            ->add('name', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['max' => 255]),
                ],
            ])
            ->add('lat', HiddenType::class, [
                'required' => false,
                'constraints' => [
                    new GreaterThanOrEqual(['value' => -90.0]),
                    new LessThanOrEqual(['value' => 90.0]),
                ],
            ])
            ->add('lng', HiddenType::class, [
                'required' => false,
                'constraints' => [
                    new GreaterThanOrEqual(['value' => -180.0]),
                    new LessThanOrEqual(['value' => 180.0]),
                ],
            ])
        ;
    }
}
