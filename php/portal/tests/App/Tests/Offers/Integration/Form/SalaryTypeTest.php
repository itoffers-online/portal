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

use App\Offers\Form\Type\Offer\SalaryType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

final class SalaryTypeTest extends TypeTestCase
{
    protected function getExtensions() : array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    public function test_submit_only_min() : void
    {
        $formData = [
            'min' => 1_500,
            'max' => null,
        ];
        $form = $this->factory->create(SalaryType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());
        $this->assertTrue($form->get('min')->isValid());
        $this->assertFalse($form->get('max')->isValid());
    }

    public function test_submit_only_max() : void
    {
        $formData = [
            'min' => null,
            'max' => 1_500,
        ];
        $form = $this->factory->create(SalaryType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());
        $this->assertFalse($form->get('min')->isValid());
        $this->assertTrue($form->get('max')->isValid());
    }

    public function test_submit_min_greater_than_max() : void
    {
        $formData = [
            'min' => 5_000,
            'max' => 1_500,
        ];
        $form = $this->factory->create(SalaryType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());
        $this->assertTrue($form->get('min')->isValid());
        $this->assertFalse($form->get('max')->isValid());
    }

    public function test_submit_empty() : void
    {
        $formData = [];
        $form = $this->factory->create(SalaryType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
    }
}
