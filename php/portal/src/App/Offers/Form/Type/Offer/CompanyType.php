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

use App\Offers\Validator\Constraints\HtmlTextLength;
use App\Offers\Validator\Constraints\NotContainsEmoji;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

final class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length(['min' => 3, 'max' => 255]),
                    new NotContainsEmoji(),
                ],
            ])
            ->add('url', TextType::class, [
                'constraints' => [
                    new Url(),
                    new NotBlank(),
                    new Length(['min' => 3, 'max' => 2_048]),
                ],
            ])
            ->add('logo', FileType::class, [
                'required' => false,
                'constraints' => [
                    new Image([
                        'minWidth' => 150,
                        'minHeight' => 150,
                        'maxWidth' => 1_000,
                        'maxHeight' => 1_000,
                        'allowLandscape' => false,
                        'allowPortrait' => false,
                        'maxSize' => '1M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'detectCorrupted' => true,
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new HtmlTextLength(['min' => 20, 'max' => 2_048]),
                    new NotContainsEmoji(),
                ],
            ])
        ;
    }
}
