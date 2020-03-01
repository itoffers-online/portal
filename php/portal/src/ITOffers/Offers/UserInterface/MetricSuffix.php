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

namespace ITOffers\Offers\UserInterface;

final class MetricSuffix
{
    const CONVERT_THRESHOLD = 1_000;

    private int $number;

    private string $locale;

    private array $binaryPrefixes = [
        1_000_000_000_000_000 => '#.##P',
        1_000_000_000_000 => '#.##T',
        1_000_000_000 => '#.##G',
        1_000_000 => '#.##M',
        1_000 => '#.#k',
        0 => '#.#',
    ];

    /**
     * @param int $number
     * @param string $locale
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(int $number, string $locale = 'en')
    {
        if (!\class_exists('NumberFormatter')) {
            throw new \RuntimeException('Metric suffix converter requires intl extension!');
        }

        $this->number = $number;
        $this->locale = $locale;

        /*
         * Workaround for 32-bit systems which ignore array ordering when
         * dropping values over 2^32-1
         */
        \krsort($this->binaryPrefixes);
    }

    public function convert() : string
    {
        $formatter = new \NumberFormatter($this->locale, \NumberFormatter::PATTERN_DECIMAL);

        foreach ($this->binaryPrefixes as $size => $unitPattern) {
            if ($size <= $this->number) {
                $value = ($this->number >= self::CONVERT_THRESHOLD) ? $this->number / (double) $size : $this->number;
                $formatter->setPattern($unitPattern);

                return $formatter->format($value);
            }
        }

        return $formatter->format($this->number);
    }
}
