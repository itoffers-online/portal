<?php

declare(strict_types=1);

namespace App\Form\Type\Offer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class ChannelsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('facebook_group', CheckboxType::class, [
                'required' => false,
            ])
        ;
    }
}
