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

use App\Offers\Form\Type\Offer\Description\RequirementsType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

final class RequirementsTypeTest extends TypeTestCase
{
    protected function getExtensions() : array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    public function test_submit_empty_skills() : void
    {
        $formData = [
            'description' => \str_repeat('a', 150),
            'skills' => [],
        ];
        $form = $this->factory->create(RequirementsType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
    }

    public function test_submit_skills() : void
    {
        $formData = [
            'description' => \str_repeat('a', 150),
            'skills' => [
                [
                    'skill' => 'skill_01',
                    'required' => 0,
                    'experience' => 5,
                ],
                [
                    'skill' => 'skill_02',
                    'required' => 0,
                    'experience' => 5,
                ],
            ],
        ];
        $form = $this->factory->create(RequirementsType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->asserttrue($form->isValid());
    }

    public function test_submit_too_many_skills() : void
    {
        $formData = [
            'description' => \str_repeat('a', 150),
            'skills' => \array_map(
                function (int $i) {
                    return [
                        'skill' => 'skill_' . $i,
                        'required' => 0,
                        'experience' => 5,
                    ];
                },
                \range(0, 100)
            ),
        ];
        $form = $this->factory->create(RequirementsType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());
    }

    public function test_submit_not_unique_skills() : void
    {
        $formData = [
            'description' => \str_repeat('a', 150),
            'skills' => [
                [
                    'skill' => 'test',
                    'required' => 0,
                    'experience' => 5,
                ],
                [
                    'skill' => 'test',
                    'required' => 0,
                    'experience' => 5,
                ],
            ],
        ];
        $form = $this->factory->create(RequirementsType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());
    }

    public function test_submit_skill_with_emoji() : void
    {
        $formData = [
            'description' => \str_repeat('a', 150),
            'skills' => [
                [
                    'skill' => 'test 😅',
                    'required' => 0,
                    'experience' => 5,
                ],
            ],
        ];
        $form = $this->factory->create(RequirementsType::class, null, []);
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());
    }
}
