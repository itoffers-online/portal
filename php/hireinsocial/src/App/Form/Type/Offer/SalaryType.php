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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class SalaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('min', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new GreaterThan(['value' => 0]),
                ],
            ])
            ->add('max', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new GreaterThan(['value' => 0]),
                    new Callback(
                        [
                            'callback' => function ($value, ExecutionContextInterface $context, $payload) {
                                /** @var \Symfony\Component\Form\Form $form */
                                $form = $context->getRoot();

                                if (null === $value && null === $form->get('salary')->get('min')->getData()) {
                                    return ;
                                }

                                if ((int) $form->get('salary')->get('min')->getData() >= (int) $value) {
                                    $context->addViolation('This value should be greater than {{ compared_value }}.', ['{{ compared_value }}' => $value]);
                                }
                            },
                        ]
                    ),
                ],
            ])
            ->add('currency', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'PLN' => 'PLN',
                    'EUR' => 'EUR',
                    'USD' => 'USD',
                ],
                'data' => 'PLN'
            ])
            ->add('net', CheckboxType::class, [
                'required' => false,
            ])
        ;
    }
}
