<?php

declare (strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Form\Type\Offer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class SalaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('min', IntegerType::class, [
                'constraints' => [
                    new GreaterThan(['value' => 0])
                ]
            ])
            ->add('max', IntegerType::class, [
                'constraints' => [
                    new GreaterThan(['value' => 0]),
                    new Callback(
                        [
                            'callback' => function ($value, ExecutionContextInterface $context, $payload) {
                                /** @var \Symfony\Component\Form\Form $form */
                                $form = $context->getRoot();

                                if ((int) $form->get('salary')->get('min')->getData() >= (int) $value) {
                                    $context->addViolation('This value should be greater than {{ compared_value }}.', ['{{ compared_value }}' => $value]);
                                }
                            }
                        ]
                    )
                ]
            ])
            ->add('currency', ChoiceType::class, [
                'choices' => [
                    'PLN' => 'PLN',
                    'EUR' => 'EUR',
                    'USD' => 'USD'
                ],
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('net', CheckboxType::class, [
                'required' => false
            ])
        ;
    }
}