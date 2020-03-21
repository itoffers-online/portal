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

use App\Offers\Form\Type\Offer\PositionType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

final class PositionTypeTest extends TypeTestCase
{
    protected function getExtensions() : array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    public function test_submit_valid_position() : void
    {
        $formData = [
            'seniorityLevel' => 0,
            'name' => 'Software Developer',
        ];
        $form = $this->factory->create(PositionType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $this->assertTrue($form->get('seniorityLevel')->isValid());
        $this->assertTrue($form->get('name')->isValid());
    }

    /**
     * @dataProvider seniorityLevels
     */
    public function test_submit_position_with_seniority_level_in_the_name(string $seniorityLevel) : void
    {
        $formData = [
            'seniorityLevel' => 0,
            'name' => $seniorityLevel . ' Software Developer',
        ];
        $form = $this->factory->create(PositionType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());
        $this->assertTrue($form->get('seniorityLevel')->isValid());
        $this->assertFalse($form->get('name')->isValid());
    }

    public function seniorityLevels() : \Generator
    {
        yield [
            'ninja',
        ];
        yield [
            'intern',
        ];
        yield [
            'junior',
        ];
        yield [
            'senior',
        ];
        yield [
            'mid',
        ];
    }
}
