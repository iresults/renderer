<?php

declare(strict_types=1);

namespace Iresults\Renderer\Pdf\Wrapper\MpdfWrapper;

use Iresults\Renderer\Pdf\Wrapper\Exception\InvalidFontNameException;

use function func_get_args;
use function is_array;

/**
 * Wrapper class for the mPDF library
 *
 * @psalm-import-type MpdfConstructorConfiguration from MpdfWrapperInterface
 */
class V8 extends \Mpdf\Mpdf implements MpdfWrapperInterface
{
    /**
     * @var MpdfConstructorConfiguration
     */
    protected $overwriteDefaults = [
    ];

    /**
     * @param MpdfConstructorConfiguration|string $config
     * @param string                              $format
     * @param int                                 $default_font_size
     * @param string                              $default_font
     * @param int                                 $mgl
     * @param int                                 $mgr
     * @param int                                 $mgt
     * @param int                                 $mgb
     * @param int                                 $mgh
     * @param int                                 $mgf
     * @param string                              $orientation
     *
     * @throws \Mpdf\MpdfException
     *
     * @noinspection PhpMissingParamTypeInspection
     */
    public function __construct(
        $config = [],
        $format = 'A4',
        $default_font_size = 0,
        $default_font = '',
        $mgl = 15,
        $mgr = 15,
        $mgt = 16,
        $mgb = 16,
        $mgh = 9,
        $mgf = 9,
        $orientation = 'P',
    ) {
        parent::__construct(is_array($config) ? $config : func_get_args());

        $this->_initializeObject();

        return $this;
    }

    public function addFontDirectoryPath(string $fontDirectoryPath): MpdfWrapperInterface
    {
        if ('/' !== substr($fontDirectoryPath, -1)) {
            $fontDirectoryPath .= '/';
        }

        $this->AddFontDirectory($fontDirectoryPath);

        return $this;
    }

    public function registerFonts(array $fontDataCollection): MpdfWrapperInterface
    {
        if (is_array($fontDataCollection)) {
            foreach ($fontDataCollection as $fontName => $fontData) {
                if (strtolower($fontName) !== $fontName) {
                    throw new InvalidFontNameException('Font name must be lower case', 1392652327);
                }
            }

            $this->fontdata = array_merge_recursive($this->fontdata, $fontDataCollection);

            foreach ($fontDataCollection as $fontName => $fontData) {
                if (isset($fontData['R']) && $fontData['R']) {
                    $this->available_unifonts[] = $fontName;
                }
                if (isset($fontData['B']) && $fontData['B']) {
                    $this->available_unifonts[] = $fontName . 'B';
                }
                if (isset($fontData['I']) && $fontData['I']) {
                    $this->available_unifonts[] = $fontName . 'I';
                }
                if (isset($fontData['BI']) && $fontData['BI']) {
                    $this->available_unifonts[] = $fontName . 'BI';
                }
            }
        }

        return $this;
    }

    public function registerFont(string $fontName, array $fontData): MpdfWrapperInterface
    {
        if (strtolower($fontName) !== $fontName) {
            throw new InvalidFontNameException('Font name must be lower case', 1392652327);
        }

        return $this->registerFonts([$fontName => $fontData]);
    }

    /**
     * Set additional object properties
     */
    protected function _initializeObject()
    {
        // Overwrite defaults
        foreach ($this->overwriteDefaults as $key => $value) {
            $this->$key = $value;
        }
    }
}
