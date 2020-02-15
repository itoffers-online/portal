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

namespace App\Tests\Offers\Unit\Email;

use App\Offers\Email\Parser;
use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
    /**
     * @dataProvider examplesProvider
     */
    public function test_email_parser(string $email, string $local, string $tag, string $domain) : void
    {
        $parser = new Parser($email);

        $this->assertEquals($local, $parser->parse()->local());
        $this->assertEquals($tag, $parser->parse()->tag());
        $this->assertEquals($domain, $parser->parse()->domain());
        $this->assertEquals($email, $parser->parse()->toString());
    }

    public function examplesProvider() : array
    {
        return [
            ['norbert@itoffers.online', 'norbert', '', 'itoffers.online'],
            ['norbert+tag@itoffers.online', 'norbert', 'tag', 'itoffers.online'],
            ['apply+developer-stowarzyszenie-kodu-V5rOoZW@itoffers.online', 'apply', 'developer-stowarzyszenie-kodu-V5rOoZW', 'itoffers.online'],
        ];
    }
}
