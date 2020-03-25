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

use App\Offers\Validator\Constraints\NotContainsEmoji;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

final class ContactType extends AbstractType
{
    public const RECRUITER_TYPE = "0";

    public const EXTERNAL_SOURCE_TYPE = "1";

    private const RECRUITER_GROUP = 'recruiter';

    private const EXTERNAL_SOURCE_GROUP = 'external_source';

    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'empty_data' => self::RECRUITER_GROUP,
                'choices' => [
                    self::RECRUITER_TYPE,
                    self::EXTERNAL_SOURCE_TYPE,
                ],
                'constraints' => [
                    new NotBlank(['groups' => [self::RECRUITER_GROUP, self::EXTERNAL_SOURCE_GROUP]]),
                ],
            ])
            ->add('name', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['groups' => self::RECRUITER_GROUP]),
                    new Length(['min' => 3, 'max' => 255, 'groups' => self::RECRUITER_GROUP]),
                    new NotContainsEmoji(['groups' => self::RECRUITER_GROUP]),
                ],
            ])
            ->add('email', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['groups' => self::RECRUITER_GROUP]),
                    new Email(['groups' => self::RECRUITER_GROUP]),
                ],
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['min' => 6, 'max' => 16]),
                    new NotContainsEmoji(['groups' => self::RECRUITER_GROUP]),
                ],
            ])
            ->add('url', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['groups' => self::EXTERNAL_SOURCE_GROUP]),
                    new Url(['groups' => self::EXTERNAL_SOURCE_GROUP]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'validation_groups' => static function (FormInterface $form) {
                $data = $form->getData();

                switch ($data['url']) {
                    case self::EXTERNAL_SOURCE_TYPE:
                        return [self::EXTERNAL_SOURCE_GROUP];
                    case self::RECRUITER_TYPE:
                        return [self::RECRUITER_GROUP];
                    default:
                        return [];
                }
            },
        ]);
    }
}
