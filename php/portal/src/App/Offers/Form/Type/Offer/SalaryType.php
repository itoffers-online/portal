<?php

declare(strict_types=1);

/*
 * This file is part of the itoffers.online project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Offers\Form\Type\Offer;

use ITOffers\Offers\Application\Query\Offer\Model\Offer\Salary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class SalaryType extends AbstractType
{
    private const SALARY_GROUP = 'salary';

    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('min', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['groups' => [self::SALARY_GROUP]]),
                    new GreaterThan(['value' => 0, 'groups' => [self::SALARY_GROUP]]),
                ],
            ])
            ->add('max', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['groups' => [self::SALARY_GROUP]]),
                    new GreaterThan(['value' => 0, 'groups' => [self::SALARY_GROUP]]),
                    new Callback(
                        [
                            'callback' => function ($value, ExecutionContextInterface $context, $payload) {
                                /** @var Form $form */
                                $form = $context->getObject()->getParent();

                                $min = $form->get('min')->getData();

                                if ($value < $min) {
                                    $context->addViolation('This value should be greater than {{ compared_value }}.', ['{{ compared_value }}' => $value]);
                                }
                            },
                            'groups' => [self::SALARY_GROUP],
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
                'constraints' => [
                    new NotBlank(['groups' => [self::SALARY_GROUP]]),
                ],
            ])
            ->add('net', CheckboxType::class, [
                'required' => false,
            ])
            ->add('period_type', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'per month' => Salary::PERIOD_TYPE_MONTH,
                    'per hour' => Salary::PERIOD_TYPE_HOUR,
                    'per day' => Salary::PERIOD_TYPE_DAY,
                    'per week' => Salary::PERIOD_TYPE_WEEK,
                    'per year' => Salary::PERIOD_TYPE_YEAR,
                    'in total' => Salary::PERIOD_TYPE_IN_TOTAL,
                ],
                'constraints' => [
                    new NotBlank(['groups' => [self::SALARY_GROUP]]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'validation_groups' => static function (FormInterface $form) {
                $data = $form->getData();


                if (null !== $data['min'] || null !== $data['max'] || null !== $data['currency'] || null !== $data['period_type']) {
                    return [self::SALARY_GROUP];
                }

                return [];
            },
        ]);
    }
}
