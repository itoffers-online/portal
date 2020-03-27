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

use App\Offers\Form\Type\Offer\ContactType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

final class ContactTypeTest extends TypeTestCase
{
    protected function getExtensions() : array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    public function test_recruiter_contact() : void
    {
        $formData = [
            'type' => ContactType::RECRUITER_TYPE,
            'name' => 'Norbert',
            'email' => 'contact@norbert.tech',
            'phone' => null,
            'url' => null,
        ];

        $form = $this->factory->create(ContactType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $this->assertEquals(
            $formData,
            [
                'type' => ContactType::RECRUITER_TYPE,
                'name' => 'Norbert',
                'email' => 'contact@norbert.tech',
                'phone' => null,
                'url' => null,
            ]
        );
    }

    public function test_external_source() : void
    {
        $formData = [
            'type' => ContactType::EXTERNAL_SOURCE_TYPE,
            'name' => null,
            'email' => null,
            'phone' => null,
            'url' => 'https://itoffers.online',
        ];

        $form = $this->factory->create(ContactType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $this->assertEquals(
            $formData,
            [
                'type' => ContactType::EXTERNAL_SOURCE_TYPE,
                'name' => null,
                'email' => null,
                'phone' => null,
                'url' => 'https://itoffers.online',
            ]
        );
    }

    public function test_empty_form() : void
    {
        $formData = [
            'type' => null,
            'name' => null,
            'email' => null,
            'phone' => null,
            'url' => null,
        ];

        $form = $this->factory->create(ContactType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());

        $this->assertFalse($form->get('type')->isValid());
    }

    public function test_invalid_recruiter() : void
    {
        $formData = [
            'type' => ContactType::RECRUITER_TYPE,
            'name' => null,
            'email' => null,
            'phone' => null,
            'url' => 'https://onet.pl',
        ];

        $form = $this->factory->create(ContactType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());

        $this->assertTrue($form->get('type')->isValid());
    }

    public function test_invalid_external_source() : void
    {
        $formData = [
            'type' => ContactType::EXTERNAL_SOURCE_TYPE,
            'name' => 'Norbert',
            'email' => 'contact@itoffers.online',
            'phone' => null,
            'url' => null,
        ];

        $form = $this->factory->create(ContactType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());

        $this->assertTrue($form->get('type')->isValid());
    }
}
