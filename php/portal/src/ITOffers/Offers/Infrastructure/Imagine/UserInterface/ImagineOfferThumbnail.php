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

use function ceil;
use Imagine\Gd\Font;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use ITOffers\Offers\Application\Query\Offer\Model\Offer;
use ITOffers\Offers\UserInterface\OfferExtension;
use ITOffers\Offers\UserInterface\OfferThumbnail;
use function round;

final class ImagineOfferThumbnail implements OfferThumbnail
{
    /**
     * @var string
     */
    private $projectRootDir;

    /**
     * @var OfferExtension
     */
    private $offerExtension;

    /**
     * @var ImagineColors
     */
    private $colors;

    public function __construct(string $projectRootDir, OfferExtension $offerExtension)
    {
        $this->projectRootDir = $projectRootDir;
        $this->offerExtension = $offerExtension;
        $this->colors = new ImagineColors();
    }

    public function large(Offer $offer, bool $force = false) : string
    {
        $destinationPath = $this->projectRootDir . '/public/assets/dist/img/offer/' . $offer->createdAt()->format('Y_m_d') . '/' . $offer->emailHash() . '.png';

        if (\file_exists($destinationPath) && !$force) {
            return $destinationPath;
        }

        $imagine = new Imagine();
        $size  = new Box($width = 1200, $height = 640);

        $paddingTop = 120;
        $paddingLeft = 20;
        $paddingRight = 20;
        $specializationSizeX = 120;
        $specializationSizeY = 120;

        $logoFontWhite = new Font(
            $this->fontPath('WorkSans-Regular'),
            $logoFontSize = 14,
            $this->colors->white()
        );
        $logoFontGreen = new Font(
            $this->fontPath('WorkSans-Regular'),
            $logoFontSize,
            $this->colors->green()
        );
        $textFont = new Font(
            $this->fontPath('WorkSans-Light'),
            $headline1Size = 12,
            $this->colors->text()
        );
        $headline1Font = new Font(
            $this->fontPath('WorkSans-Light'),
            $headline1Size = 30,
            $this->colors->text()
        );
        $headline2Font = new Font(
            $this->fontPath('WorkSans-Light'),
            $headline2Size = 20,
            $this->colors->blue()
        );
        $headline3Font = new Font(
            $this->fontPath('WorkSans-Regular'),
            $headline2Size = 16,
            $this->colors->gray()
        );
        $headline4Font = new Font(
            $this->fontPath('WorkSans-Bold'),
            $headline2Size = 12,
            $this->colors->gray()
        );
        $salaryFontBold = new Font(
            $this->fontPath('WorkSans-Bold'),
            $salaryFontSize = 16,
            $this->colors->text()
        );
        $salaryFontRegular = new Font(
            $this->fontPath('WorkSans-Regular'),
            $salaryFontSize = 16,
            $this->colors->text()
        );
        $applyFont = new Font(
            $this->fontPath('WorkSans-Regular'),
            $salaryFontSize = 18,
            $this->colors->white()
        );

        $image = $imagine->create($size, $this->colors->light());

        // Header
        $image->draw()
            ->rectangle(
                new Point(0, 0),
                new Point($width, $paddingTop - 40),
                $this->colors->dark(),
                $fill = true
            );

        $logoTextIT = 'it';
        $logoTextOffers = 'offers';
        $logoTextOnline = '.online';

        $image->draw()->text(
            $logoTextIT,
            $logoFontWhite,
            new Point($paddingLeft, $logoMarginTop = 30),
        );

        $image->draw()->text(
            $logoTextOffers,
            $logoFontGreen,
            new Point(
                $paddingLeft + $logoFontWhite->box($logoTextIT)->getWidth(),
                $logoMarginTop
            ),
        );
        $image->draw()->text(
            $logoTextOnline,
            $logoFontWhite,
            new Point(
                $paddingLeft + $logoFontWhite->box($logoTextIT)->getWidth() + $logoFontGreen->box($logoTextOffers)->getWidth(),
                $logoMarginTop
            ),
        );

        // Specialization
        $specLogo = $imagine->open($this->specializationLogoPath($offer->specializationSlug()))
            ->resize(new Box($specializationSizeX, $specializationSizeY));

        $image->draw()
            ->rectangle(
                new Point($paddingLeft, $paddingTop),
                new Point($paddingLeft + $specializationSizeX, $paddingTop + $specializationSizeY),
                $this->colors->white(),
                $fill = true
            );

        $image->paste(
            $specLogo,
            new Point($paddingLeft, $paddingTop),
        );

        // Job
        $image->draw()->text(
            $offer->position()->name(),
            $headline1Font,
            new Point($paddingLeft + $specializationSizeY + 20, $paddingTop),
            0,
            $jobTitleMaxWidth = 620
        );

        $image->draw()->text(
            $offer->company()->name(),
            $headline2Font,
            new Point(
                $paddingLeft + $specializationSizeY + 20,
                $paddingTop
                + $headline1Font->box($offer->position()->name())->getHeight()
                + ((int) ceil($headline1Font->box($offer->position()->name())->getWidth() / $jobTitleMaxWidth)) * $headline1Font->box($offer->position()->name())->getHeight()
            ),
            0,
            $jobTitleMaxWidth
        );

        $image->draw()
            ->rectangle(
                new Point(0, $bottomBoxMarginTop = 350),
                new Point($width, $height),
                $this->colors->white(),
                $fill = true
            );

        $image->draw()
            ->line(
                new Point(0, $bottomBoxMarginTop),
                new Point($width, $bottomBoxMarginTop),
                $this->colors->border()
            );

        // Job Description
        $image->draw()->text(
            'Job Description',
            $headline3Font,
            new Point(
                $paddingLeft,
                $bottomBoxMarginTop + $bottomBoxPaddingTop = 40
            ),
        );

        // Job Typ
        $image->draw()->text(
            'Job Type',
            $headline4Font,
            new Point(
                $paddingLeft + 230,
                $bottomBoxMarginTop + $bottomBoxPaddingTop
            ),
        );

        $jobTypeIcon = $imagine->open($this->iconFilePath('file-alt'))
            ->resize(new Box(13, 15));

        $image->paste(
            $jobTypeIcon,
            new Point(
                $paddingLeft + 230,
                $bottomBoxMarginTop + $bottomBoxPaddingTop + 30
            ),
        );

        $image->draw()->text(
            $offer->contract()->type(),
            $textFont,
            new Point(
                $paddingLeft + 250,
                $bottomBoxMarginTop + $bottomBoxPaddingTop + 30
            ),
        );

        // Seniority
        $image->draw()->text(
            'Seniority Level',
            $headline4Font,
            new Point(
                $paddingLeft + 230,
                $bottomBoxMarginTop + $bottomBoxPaddingTop + 80
            ),
        );

        $layerGroupIcon = $imagine->open($this->iconFilePath('layer-group'))
            ->resize(new Box(13, 15));

        $image->paste(
            $layerGroupIcon,
            new Point(
                $paddingLeft + 230,
                $bottomBoxMarginTop + $bottomBoxPaddingTop + 110
            ),
        );

        $image->draw()->text(
            $this->offerExtension->seniorityLevelName($offer->position()->seniorityLevel()),
            $textFont,
            new Point(
                $paddingLeft + 250,
                $bottomBoxMarginTop + $bottomBoxPaddingTop + 110
            ),
        );

        // Work Type
        $image->draw()->text(
            'Work Type',
            $headline4Font,
            new Point(
                $paddingLeft + 530,
                $bottomBoxMarginTop + $bottomBoxPaddingTop
            ),
        );

        $workTypeIcon = $imagine->open($this->iconFilePath('laptop-code'))
            ->resize(new Box(13, 15));

        $image->paste(
            $workTypeIcon,
            new Point(
                $paddingLeft + 530,
                $bottomBoxMarginTop + $bottomBoxPaddingTop + 30
            ),
        );

        $image->draw()->text(
            $this->offerExtension->workType($offer->location()),
            $textFont,
            new Point(
                $paddingLeft + 550,
                $bottomBoxMarginTop + $bottomBoxPaddingTop + 30
            ),
        );

        // Position
        $image->draw()->text(
            'Position',
            $headline4Font,
            new Point(
                $paddingLeft + 530,
                $bottomBoxMarginTop + $bottomBoxPaddingTop + 80
            ),
        );
        $image->draw()->text(
            $offer->position()->name(),
            $textFont,
            new Point(
                $paddingLeft + 530,
                $bottomBoxMarginTop + $bottomBoxPaddingTop + 110
            ),
        );

        // Salary
        $image->draw()
            ->rectangle(
                new Point($salaryBoxLeftX = 800, $paddingTop),
                new Point($width - $paddingRight, $salaryBoxHeight = $height - 120),
                $this->colors->white(),
                $fill = true
            );
        $image->draw()
            ->rectangle(
                new Point($salaryBoxLeftX, $paddingTop),
                new Point($width - $paddingRight, $salaryBoxHeight),
                $this->colors->border(),
                $fill = false
            );

        $salaryBoxWidth = $width - $paddingRight - $salaryBoxLeftX;
        $salaryText = $offer->salary()
            ? \sprintf(
                "%s %s - %s",
                $offer->salary()->currencyCode(),
                $this->offerExtension->salaryInteger($offer->salary()->min()),
                $this->offerExtension->salaryInteger($offer->salary()->max())
            )
            : $offer->position()->name();

        $salaryBoxPaddingTop = 30;

        $image->draw()->text(
            $salaryText,
            $salaryFontBold,
            new Point(
                $salaryBoxLeftX + (int) ceil(($salaryBoxWidth - $salaryFontBold->box($salaryText)->getWidth()) / 2),
                $paddingTop + $salaryBoxPaddingTop
            ),
            0,
            $salaryBoxWidth - 20
        );

        $salaryPeriodicityText = $offer->salary()
            ? $this->offerExtension->salaryType($offer->salary())
            : $offer->company()->name();

        $image->draw()->text(
            $salaryPeriodicityText,
            $salaryFontRegular,
            new Point(
                $salaryBoxLeftX + (int) ceil(($salaryBoxWidth - $salaryFontRegular->box($salaryPeriodicityText)->getWidth()) / 2),
                $paddingTop + $salaryBoxPaddingTop + $salaryFontBold->box($salaryText)->getHeight() + 10
            ),
            0,
            $salaryBoxWidth - 20
        );

        $image->draw()
            ->rectangle(
                new Point(
                    $salaryBoxLeftX + $applyButtonMarginLeft = 60,
                    $paddingTop + $applyButtonMarginTop = 180
                ),
                new Point(
                    $width - $paddingRight - $applyButtonMarginLeft,
                    $paddingTop + $applyButtonMarginTop + $applyButtonHeight = 80
                ),
                $this->colors->primary(),
                $fill = true
            );

        $applyText = 'Apply';
        $image->draw()->text(
            $applyText,
            $applyFont,
            new Point(
                $salaryBoxLeftX + (int) round(($salaryBoxWidth - $applyFont->box($applyText)->getWidth()) / 2),
                $paddingTop + $applyButtonMarginTop + 30
            ),
            0,
            $salaryBoxWidth - 20
        );

        $image->draw()
            ->line(
                new Point($salaryBoxLeftX, $salaryLocationLineTop = $paddingTop + $applyButtonMarginTop + $applyButtonHeight + 60),
                new Point($width - $paddingRight, $salaryLocationLineTop),
                $this->colors->border()
            );

        $locationIcon = $imagine->open($this->iconFilePath('map-marker-alt'))
            ->resize(new Box(13, 15));

        $image->paste(
            $locationIcon,
            new Point(
                $salaryBoxLeftX + 40,
                $salaryLocationLineTop = $paddingTop + $applyButtonMarginTop + $applyButtonHeight + 90
            ),
        );

        $image->draw()->text(
            $this->offerExtension->locationText($offer->location()),
            $textFont,
            new Point(
                $salaryBoxLeftX + 70,
                $salaryLocationLineTop = $paddingTop + $applyButtonMarginTop + $applyButtonHeight + 90
            ),
            0,
            $salaryBoxWidth - \ceil($salaryBoxWidth / 2 / 2)
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

    private function iconFilePath(string $iconName) : string
    {
        return $this->projectRootDir . '/public/assets/img/icon/png/' . $iconName . '.png';
    }

    private function specializationLogoPath(string $specializationSlug) : string
    {
        return $this->projectRootDir . '/public/assets/img/specialization/jpg/' . $specializationSlug . '.jpg';
    }
}
