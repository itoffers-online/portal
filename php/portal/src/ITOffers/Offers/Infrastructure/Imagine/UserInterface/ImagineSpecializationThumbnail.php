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

namespace ITOffers\Offers\Infrastructure\Imagine\UserInterface;

use Imagine\Gd\Font;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use ITOffers\Offers\Application\Query\Specialization\Model\Specialization;
use ITOffers\Offers\UserInterface\SpecializationExtension;
use ITOffers\Offers\UserInterface\SpecializationThumbnail;

final class ImagineSpecializationThumbnail implements SpecializationThumbnail
{
    /**
     * @var string
     */
    private $projectRootDir;

    /**
     * @var ImagineColors
     */
    private $colors;

    /**
     * @var SpecializationExtension
     */
    private $specializationExtension;

    public function __construct(string $projectRootDir, SpecializationExtension $specializationExtension)
    {
        $this->projectRootDir = $projectRootDir;
        $this->colors = new ImagineColors();
        $this->specializationExtension = $specializationExtension;
    }

    public function large(Specialization $specialization, bool $force = false) : string
    {
        $destinationPath = $this->projectRootDir . '/public/assets/dist/img/specialization/' . $specialization->slug() . '.png';

        if (\file_exists($destinationPath) && !$force) {
            return $destinationPath;
        }

        $imagine = new Imagine();
        $size  = new Box($width = 1200, $height = 640);

        $image = $imagine->create($size, $this->colors->light());

        $headlineFont = new Font(
            $this->fontPath('WorkSans-Regular'),
            $headline1Size = 45,
            $this->colors->white()
        );

        $logoFontWhite = new Font(
            $this->fontPath('WorkSans-Light'),
            $logoFontSize = 36,
            $this->colors->white()
        );
        $logoFontGreen = new Font(
            $this->fontPath('WorkSans-Light'),
            $logoFontSize,
            $this->colors->green()
        );

        $image->draw()
            ->rectangle(
                new Point(0, 0),
                new Point($width, $height),
                $this->colors->dark(),
                $fill = true
            );

        // Specialization
        $specLogo = $imagine->open($this->specializationLogoPath($specialization->slug()))
            ->resize(new Box($specializationSizeX = 300, $specializationSizeY = 300));

        $image->draw()
            ->rectangle(
                new Point($paddingLeft = 60, $paddingTop = \ceil($height / 2) - \ceil($specializationSizeY / 2)),
                new Point($paddingLeft + $specializationSizeX, $paddingTop + $specializationSizeY),
                $this->colors->white(),
                $fill = true
            );

        $image->paste(
            $specLogo,
            new Point($paddingLeft, $paddingTop)
        );

        $image->draw()
            ->rectangle(
                new Point($paddingLeft, $paddingTop),
                new Point($paddingLeft + $specializationSizeX, $paddingTop + $specializationSizeY),
                $this->colors->text(),
                $fill = false,
                2
            );

        // Logo
        $logoTextIT = 'it';
        $logoTextOffers = 'offers';
        $logoTextOnline = '.online';

        $image->draw()->text(
            $logoTextIT,
            $logoFontWhite,
            new Point(
                $paddingLeft + $specializationSizeX + $imageMarginRight = 25,
                $paddingTop + $logoMarginTop = 70
            )
        );

        $image->draw()->text(
            $logoTextOffers,
            $logoFontGreen,
            new Point(
                $paddingLeft + $specializationSizeX + $imageMarginRight +$logoFontWhite->box($logoTextIT)->getWidth(),
                $paddingTop + $logoMarginTop
            )
        );
        $image->draw()->text(
            $logoTextOnline,
            $logoFontWhite,
            new Point(
                $paddingLeft + $specializationSizeX + $imageMarginRight +$logoFontWhite->box($logoTextIT)->getWidth() + $logoFontGreen->box($logoTextOffers)->getWidth(),
                $paddingTop + $logoMarginTop
            )
        );

        $image->draw()->text(
            $this->specializationExtension->title($specialization->slug()),
            $headlineFont,
            new Point(
                $paddingLeft + $specializationSizeX + $imageMarginRight,
                $paddingTop
            )
        );

        if (!\file_exists(\dirname($destinationPath))) {
            \mkdir(\dirname($destinationPath), 0777, true);
        }

        $image->save(
            $destinationPath,
            $options = [
                'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
                'resolution-x' => 1240,
                'resolution-y' => 640,
            ]
        );

        return $destinationPath;
    }

    private function fontPath(string $fontName) : string
    {
        return $this->projectRootDir . '/public/assets/font/work-sans/' . $fontName . '.ttf';
    }

    private function specializationLogoPath(string $specializationSlug) : string
    {
        return $this->projectRootDir . '/public/assets/img/specialization/jpg/' . $specializationSlug . '.jpg';
    }
}
