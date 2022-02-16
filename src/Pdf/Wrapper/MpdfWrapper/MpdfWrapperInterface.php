<?php
declare(strict_types=1);

namespace Iresults\Renderer\Pdf\Wrapper\MpdfWrapper;

use Iresults\Renderer\Exception;
use Iresults\Renderer\Pdf\Wrapper\Exception\InvalidFontNameException;

/**
 * Interface for wrappers around the mPDF library
 *
 * @psalm-type MpdfConstructorConfiguration=array{ mode: string, format: string, default_font_size: float, default_font: string, margin_left: float, margin_right: float, margin_top: float, margin_bottom: float, margin_header: float, margin_footer: float, orientation: string}
 */
interface MpdfWrapperInterface
{
    /**
     * Add a directory where fonts are stored in
     *
     * @param string $fontDirectoryPath
     * @return MpdfWrapperInterface
     */
    public function addFontDirectoryPath(string $fontDirectoryPath): MpdfWrapperInterface;

    /**
     * @param string|scalar $html
     * @param int           $mode See \Mpdf\HTMLParserMode
     * @return void
     */
    public function writeHtml($html, int $mode = 0);

    /**
     * Register the given fonts
     *
     * Example $fontData:
     *
     * array(
     *    "dejavusanscondensed" => array(
     *        'R' => "DejaVuSansCondensed.ttf",
     *        'B' => "DejaVuSansCondensed-Bold.ttf",
     *        'I' => "DejaVuSansCondensed-Oblique.ttf",
     *        'BI' => "DejaVuSansCondensed-BoldOblique.ttf",
     *    ),
     * )
     *
     * @param array $fontDataCollection
     * @return MpdfWrapperInterface
     * @throws Exception if an entry in the collection is invalid
     * @throws InvalidFontNameException if the font name is not lower case
     */
    public function registerFonts(array $fontDataCollection): MpdfWrapperInterface;

    /**
     * Register the given font
     *
     * Example $fontData:
     *
     * array(
     *    'R' => "DejaVuSansCondensed.ttf",
     *    'B' => "DejaVuSansCondensed-Bold.ttf",
     *    'I' => "DejaVuSansCondensed-Oblique.ttf",
     *    'BI' => "DejaVuSansCondensed-BoldOblique.ttf",
     * )
     *
     * @param string $fontName
     * @param array  $fontData
     * @return MpdfWrapperInterface
     * @throws InvalidFontNameException if the font name is not lower case
     */
    public function registerFont(string $fontName, array $fontData): MpdfWrapperInterface;
}
