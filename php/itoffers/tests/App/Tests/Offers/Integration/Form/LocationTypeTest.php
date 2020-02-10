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

namespace App\Tests\Offers\Integration\Form;

use App\Offers\Form\Type\Offer\LocationType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

final class LocationTypeTest extends TypeTestCase
{
    protected function getExtensions() : array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    public function test_remote_location() : void
    {
        $formData = [
            'type' => LocationType::LOCATION_REMOTE,
            'address' => null,
            'country' => null,
            'city' => null,
            'lat' => null,
            'lng' => null,
        ];
        $form = $this->factory->create(LocationType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $this->assertEquals(
            $formData,
            [
                'type' => LocationType::LOCATION_REMOTE,
                'address' => null,
                'country' => null,
                'city' => null,
                'lat' => null,
                'lng' => null,
            ]
        );
    }

    public function test_partially_remote_location_without_location() : void
    {
        $formData = [
            'type' => LocationType::LOCATION_PARTIALLY_REMOTE,
            'address' => null,
            'country' => null,
            'city' => null,
            'lat' => null,
            'lng' => null,
        ];
        $form = $this->factory->create(LocationType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $this->assertFalse($form->get('address')->isValid());
        $this->assertFalse($form->get('country')->isValid());
        $this->assertFalse($form->get('city')->isValid());
    }

    public function test_at_office_location_without_location() : void
    {
        $formData = [
            'type' => LocationType::LOCATION_AT_OFFICE,
            'address' => null,
            'country' => null,
            'city' => null,
            'lat' => null,
            'lng' => null,
        ];
        $form = $this->factory->create(LocationType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $this->assertFalse($form->get('address')->isValid());
        $this->assertFalse($form->get('country')->isValid());
        $this->assertFalse($form->get('city')->isValid());
    }
}
