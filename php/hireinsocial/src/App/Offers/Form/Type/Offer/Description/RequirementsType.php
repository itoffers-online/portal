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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class RequirementsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('skills', CollectionType::class, [
                'entry_type' => RequirementType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
                'constraints' => [
                    new Count(['max' => 50]),
                    new Callback([
                        'callback' => function ($object, ExecutionContextInterface $context, $payload) {
                            if (!\count($object)) {
                                return ;
                            }

                            $skills = \array_unique(\array_map(function (array $skillData) {
                                return \mb_strtolower($skillData['skill']);
                            }, $object));

                            if (\count($skills) !== \count($object)) {
                                $context->buildViolation('Skills are not unique.')
                                    ->addViolation();
                            }
                        },
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new Length(['min' => 0, 'max' => 2048]),
                    new NotContainsEmoji(),
                ],
            ])
        ;
    }
}
