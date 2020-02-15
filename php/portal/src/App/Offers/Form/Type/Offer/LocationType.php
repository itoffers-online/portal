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
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

final class LocationType extends AbstractType
{
    public const LOCATION_REMOTE = "0";

    public const LOCATION_PARTIALLY_REMOTE = "1";

    public const LOCATION_AT_OFFICE = "2";

    private const PARTIALLY_REMOTE_GROUP = 'partially_remote';

    private const AT_OFFICE_GROUP = 'at_office';

    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'empty_data' => self::LOCATION_REMOTE,
                'choices' => [
                    self::LOCATION_REMOTE,
                    self::LOCATION_PARTIALLY_REMOTE,
                    self::LOCATION_AT_OFFICE,
                ],
            ])
            ->add('address', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['groups' => [self::PARTIALLY_REMOTE_GROUP, self::AT_OFFICE_GROUP]]),
                ],
            ])
            ->add('country', CountryType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['groups' => [self::PARTIALLY_REMOTE_GROUP, self::AT_OFFICE_GROUP]]),
                ],
            ])
            ->add('city', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['groups' => [self::PARTIALLY_REMOTE_GROUP, self::AT_OFFICE_GROUP]]),
                    new Length(['min' => 3, 'max' => 512, 'groups' => [self::PARTIALLY_REMOTE_GROUP, self::AT_OFFICE_GROUP]]),
                    new NotContainsEmoji(['groups' => [self::PARTIALLY_REMOTE_GROUP, self::AT_OFFICE_GROUP]]),
                ],
            ])
            ->add('lat', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['groups' => [self::PARTIALLY_REMOTE_GROUP, self::AT_OFFICE_GROUP]]),
                    new GreaterThanOrEqual(['value' => -90.0, 'groups' => [self::PARTIALLY_REMOTE_GROUP, self::AT_OFFICE_GROUP]]),
                    new LessThanOrEqual(['value' => 90.0, 'groups' => [self::PARTIALLY_REMOTE_GROUP, self::AT_OFFICE_GROUP]]),
                ],
            ])
            ->add('lng', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['groups' => [self::PARTIALLY_REMOTE_GROUP, self::AT_OFFICE_GROUP]]),
                    new GreaterThanOrEqual(['value' => -180.0, 'groups' => [self::PARTIALLY_REMOTE_GROUP, self::AT_OFFICE_GROUP]]),
                    new LessThanOrEqual(['value' => 180.0, 'groups' => [self::PARTIALLY_REMOTE_GROUP, self::AT_OFFICE_GROUP]]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'validation_groups' => static function (FormInterface $form) {
                $data = $form->getData();

                switch ($data['type']) {
                    case self::LOCATION_PARTIALLY_REMOTE:
                        return [self::PARTIALLY_REMOTE_GROUP];
                    case self::LOCATION_AT_OFFICE:
                        return [self::AT_OFFICE_GROUP];
                    default:
                        return [];
                }
            },
        ]);
    }
}
