<?php
/*
 *  Copyright notice
 *
 *  (c) 2014 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *  Daniel Corn <cod@iresults.li>, iresults
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * @author COD
 * Created 14.02.14 16:17
 */


namespace Iresults\Renderer\Pdf\Wrapper;

use Iresults\Core\Iresults;
use Iresults\Renderer\Exception;
use Iresults\Renderer\Pdf\Wrapper\Exception\InvalidFontNameException;
use Iresults\Renderer\Pdf\Wrapper\Exception\InvalidFontPathException;
use mPDF as BaseMpdf;
use TTFontFile;

/**
 * Wrapper class for the mPDF library
 *
 * @property       $fontdata
 * @property array $available_unifonts
 * @package Iresults\Renderer\Pdf\Wrapper
 */
class MpdfWrapper extends BaseMpdf
{
    /**
     * A list of defaults which will be set after the mPDF initialization
     *
     * @var array
     */
    protected $overwriteDefaults = array(
        'autoLangToFont' => false,
    );

    /**
     * Paths to directories where fonts are stored in
     *
     * @var array
     */
    protected $fontDirectoryPaths = array(
        _MPDF_TTFONTPATH,
    );

    function __construct(
        $mode = '',
        $format = 'A4',
        $default_font_size = 0,
        $default_font = '',
        $mgl = 15,
        $mgr = 15,
        $mgt = 16,
        $mgb = 16,
        $mgh = 9,
        $mgf = 9,
        $orientation = 'P'
    ) {
        $this->_initializeLibrary();

        if (defined('_MPDF_SYSTEM_TTFONTS')) {
            array_unshift($this->fontDirectoryPaths, _MPDF_SYSTEM_TTFONTS);
        }

        $funcArgs = func_get_args();

        $isMinimumVersion6 = defined('mPDF_VERSION') && version_compare(mPDF_VERSION, 6.0) >= 0;
        if ($isMinimumVersion6) {
            parent::__construct(
                $mode,
                $format,
                $default_font_size,
                $default_font,
                $mgl,
                $mgr,
                $mgt,
                $mgb,
                $mgh,
                $mgf,
                $orientation
            );
        } else {
            call_user_func_array(array($this, 'mPDF'), $funcArgs);
            if (isset($this->overwriteDefaults['autoLangToFont'])) {
                $this->overwriteDefaults['useLang'] = $this->overwriteDefaults['autoLangToFont'];
            }
        }

        $this->_initializeObject();

        return $this;
    }

    /**
     * Returns the paths to directories where fonts are stored in
     *
     * @return array<string>
     */
    public function getFontDirectoryPaths()
    {
        return $this->fontDirectoryPaths;
    }

    /**
     * Sets the paths to directories where fonts are stored in
     *
     * @param array <string> $fontDirectoryPaths
     * @return $this
     */
    public function setFontDirectoryPaths($fontDirectoryPaths)
    {
        $this->fontDirectoryPaths = $fontDirectoryPaths;

        return $this;
    }

    /**
     * Adds a directory where fonts are stored in
     *
     * @param string $fontDirectoryPath
     * @return $this
     */
    public function addFontDirectoryPath($fontDirectoryPath)
    {
        if (substr($fontDirectoryPath, -1) !== '/') {
            $fontDirectoryPath .= '/';
        }
        $this->fontDirectoryPaths[] = $fontDirectoryPath;

        return $this;
    }

    /**
     * Throws an exception
     *
     * @param string $msg
     * @throws WrapperException
     */
    public function throwException($msg)
    {
        throw new WrapperException($msg);
    }

    /**
     * Registers the given fonts
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
     * @throws InvalidFontNameException if the font name is not lower case
     * @throws Exception if an entry in the collection is invalid
     * @return $this
     */
    public function registerFonts($fontDataCollection)
    {
        if (is_array($fontDataCollection)) {
            foreach ($fontDataCollection as $fontName => $fontData) {
                if (strtolower($fontName) !== $fontName) {
                    throw new InvalidFontNameException('Font name must be lower case', 1392652327);
                }

                /** @var Exception $error */
                $error = null;
                if (!$this->validateFontData($fontData, $error)) {
                    throw $error;
                    // throw new WrapperException('Given font data is invalid', 1392635396);
                }
            }


            $this->fontdata = array_merge_recursive($this->fontdata, $fontDataCollection);

            foreach ($fontDataCollection AS $fontName => $fontData) {
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

    /**
     * Registers the given font
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
     * @throws InvalidFontNameException if the font name is not lower case
     * @return $this
     */
    public function registerFont($fontName, $fontData)
    {
        if (strtolower($fontName) !== $fontName) {
            throw new InvalidFontNameException('Font name must be lower case', 1392652327);
        }

        return $this->registerFonts(array($fontName => $fontData));
    }


    /**
     * Validates the given font data
     *
     * @param array $fontData Font data to validate
     * @param       array     <mixed> $error Reference to be filled with the error
     * @return boolean Returns if the data is valid
     */
    public function validateFontData($fontData, &$error = null)
    {
        foreach ($fontData as $fontFileName) {
            if (!$this->getPathForFont($fontFileName)) {
                $allFontDirectories = implode(', ', $this->getFontDirectoryPaths());
                $error = new InvalidFontPathException(
                    sprintf('Font file "%s" not found in %s', $fontFileName, $allFontDirectories), 1392640103
                );

                return false;
            }
        }

        return true;
    }

    /**
     * Returns the path to the given font or NULL if it is not found
     *
     * @param string $fontFileName
     * @return string|NULL
     */
    public function getPathForFont($fontFileName)
    {
        $fontDirectoryPaths = $this->getFontDirectoryPaths();
        $currentFontDirectoryPath = reset($fontDirectoryPaths);
        do {
            if (file_exists($currentFontDirectoryPath . $fontFileName)) {
                return $currentFontDirectoryPath . $fontFileName;
            }
        } while ($currentFontDirectoryPath = next($fontDirectoryPaths));

        return null;
    }

    /**
     * Sets the mPDFs constants
     */
    protected function _initializeLibrary()
    {
        if (!defined('_MPDF_TTFONTDATAPATH')) {
            define('_MPDF_TTFONTDATAPATH', Iresults::getTempPath());
        }
        if (!defined('_MPDF_TEMP_PATH')) {
            define('_MPDF_TEMP_PATH', Iresults::getTempPath());
        }
    }

    /**
     * Sets additional object properties
     */
    protected function _initializeObject()
    {
        // Overwrite defaults
        foreach ($this->overwriteDefaults as $key => $value) {
            $this->$key = $value;
        }
    }


    // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    // OVERWRITES
    // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    /**
     * Throws an exception instead of terminating the script on error
     *
     * @param string $msg
     * @throws WrapperException
     */
    function Error($msg)
    {
        $this->throwException($msg);
    }

    function AddFont($family, $style = '')
    {
        if (empty($family)) {
            return;
        }
        $family = strtolower($family);
        $style = strtoupper($style);
        $style = str_replace('U', '', $style);
        if ($style == 'IB') {
            $style = 'BI';
        }
        $fontkey = $family . $style;
        // check if the font has been already added
        if (isset($this->fonts[$fontkey])) {
            return;
        }

        /*-- CJK-FONTS --*/
        if (in_array($family, $this->available_CJK_fonts)) {
            if (empty($this->Big5_widths)) {
                require(_MPDF_PATH . 'includes/CJKdata.php');
            }
            $this->AddCJKFont($family); // don't need to add style
            return;
        }
        /*-- END CJK-FONTS --*/

        // IRESULTS - COD MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
        if ($this->usingCoreFont) {
            $this->throwException("mPDF Error - problem with Font management");
        }

        $stylekey = $style;
        if (!$style) {
            $stylekey = 'R';
        }

        if (!isset($this->fontdata[$family][$stylekey]) || !$this->fontdata[$family][$stylekey]) {
            $this->throwException('mPDF Error - Font is not supported - ' . $family . ' ' . $style);
        }
        // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM

        $name = '';
        $originalsize = 0;
        $sip = false;
        $smp = false;
        $unAGlyphs = false; // mPDF 5.4.05
        $haskerninfo = false;
        $BMPselected = false;
        @include(_MPDF_TTFONTDATAPATH . $fontkey . '.mtx.php');

        $ttffile = '';
        // IRESULTS - COD MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
        /*
        if (defined('_MPDF_SYSTEM_TTFONTS')) {
            $ttffile = _MPDF_SYSTEM_TTFONTS . $this->fontdata[$family][$stylekey];
            if (!file_exists($ttffile)) {
                $ttffile = '';
            }
        }
        if (!$ttffile) {
            $ttffile = _MPDF_TTFONTPATH . $this->fontdata[$family][$stylekey];
            if (!file_exists($ttffile)) {
                die("mPDF Error - cannot find TTF TrueType font file - " . $ttffile);
            }
        }
        */

        $ttffile = $this->getPathForFont($this->fontdata[$family][$stylekey]);
        if ($ttffile === null) {
            throw new InvalidFontPathException(
                'Font file "' . $this->fontdata[$family][$stylekey] . '" not found',
                1392640103
            );
        }
        // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM

        $ttfstat = stat($ttffile);

        if (isset($this->fontdata[$family]['TTCfontID'][$stylekey])) {
            $TTCfontID = $this->fontdata[$family]['TTCfontID'][$stylekey];
        } else {
            $TTCfontID = 0;
        }


        $BMPonly = false;
        if (in_array($family, $this->BMPonly)) {
            $BMPonly = true;
        }
        $regenerate = false;
        if ($BMPonly && !$BMPselected) {
            $regenerate = true;
        } else {
            if (!$BMPonly && $BMPselected) {
                $regenerate = true;
            }
        }
        if ($this->useKerning && !$haskerninfo) {
            $regenerate = true;
        }
        // mPDF 5.4.05
        if (isset($this->fontdata[$family]['unAGlyphs']) && $this->fontdata[$family]['unAGlyphs'] && !$unAGlyphs) {
            $regenerate = true;
            $unAGlyphs = true;
        } else {
            if ((!isset($this->fontdata[$family]['unAGlyphs']) || !$this->fontdata[$family]['unAGlyphs']) && $unAGlyphs) {
                $regenerate = true;
                $unAGlyphs = false;
            }
        }
        if (!isset($name) || $originalsize != $ttfstat['size'] || $regenerate) {
            if (!class_exists('TTFontFile', false)) {
                include(_MPDF_PATH . 'classes/ttfontsuni.php');
            }
            $ttf = new TTFontFile();
            $ttf->getMetrics(
                $ttffile,
                $TTCfontID,
                $this->debugfonts,
                $BMPonly,
                $this->useKerning,
                $unAGlyphs
            ); // mPDF 5.4.05
            $cw = $ttf->charWidths;
            $kerninfo = $ttf->kerninfo;
            $haskerninfo = true;
            $name = preg_replace('/[ ()]/', '', $ttf->fullName);
            $sip = $ttf->sipset;
            $smp = $ttf->smpset;

            $desc = array(
                'Ascent'       => round($ttf->ascent),
                'Descent'      => round($ttf->descent),
                'CapHeight'    => round($ttf->capHeight),
                'Flags'        => $ttf->flags,
                'FontBBox'     => '[' . round($ttf->bbox[0]) . " " . round($ttf->bbox[1]) . " " . round(
                        $ttf->bbox[2]
                    ) . " " . round(
                        $ttf->bbox[3]
                    ) . ']',
                'ItalicAngle'  => $ttf->italicAngle,
                'StemV'        => round($ttf->stemV),
                'MissingWidth' => round($ttf->defaultWidth),
            );
            $panose = '';
            // mPDF 5.5.19
            if (count($ttf->panose)) {
                $panoseArray = array_merge(array($ttf->sFamilyClass, $ttf->sFamilySubClass), $ttf->panose);
                foreach ($panoseArray as $value) {
                    $panose .= ' ' . dechex($value);
                }
            }
            $up = round($ttf->underlinePosition);
            $ut = round($ttf->underlineThickness);
            $originalsize = $ttfstat['size'] + 0;
            $type = 'TTF';
            //Generate metrics .php file
            $s = '<?php' . "\n";
            $s .= '$name=\'' . $name . "';\n";
            $s .= '$type=\'' . $type . "';\n";
            $s .= '$desc=' . var_export($desc, true) . ";\n";
            $s .= '$up=' . $up . ";\n";
            $s .= '$ut=' . $ut . ";\n";
            $s .= '$ttffile=\'' . $ttffile . "';\n";
            $s .= '$TTCfontID=\'' . $TTCfontID . "';\n";
            $s .= '$originalsize=' . $originalsize . ";\n";
            if ($sip) {
                $s .= '$sip=true;' . "\n";
            } else {
                $s .= '$sip=false;' . "\n";
            }
            if ($smp) {
                $s .= '$smp=true;' . "\n";
            } else {
                $s .= '$smp=false;' . "\n";
            }
            if ($BMPonly) {
                $s .= '$BMPselected=true;' . "\n";
            } else {
                $s .= '$BMPselected=false;' . "\n";
            }
            $s .= '$fontkey=\'' . $fontkey . "';\n";
            $s .= '$panose=\'' . $panose . "';\n";
            if ($this->useKerning) {
                $s .= '$kerninfo=' . var_export($kerninfo, true) . ";\n";
                $s .= '$haskerninfo=true;' . "\n";
            } else {
                $s .= '$haskerninfo=false;' . "\n";
            }
            // mPDF 5.4.05
            if ($this->fontdata[$family]['unAGlyphs']) {
                $s .= '$unAGlyphs=true;' . "\n";
            } else {
                $s .= '$unAGlyphs=false;' . "\n";
            }
            $s .= "?>";
            if (is_writable(dirname(_MPDF_TTFONTDATAPATH . 'x'))) {
                $fh = fopen(_MPDF_TTFONTDATAPATH . $fontkey . '.mtx.php', "w");
                fwrite($fh, $s, strlen($s));
                fclose($fh);
                $fh = fopen(_MPDF_TTFONTDATAPATH . $fontkey . '.cw.dat', "wb");
                fwrite($fh, $cw, strlen($cw));
                fclose($fh);
                @unlink(_MPDF_TTFONTDATAPATH . $fontkey . '.cgm');
                @unlink(_MPDF_TTFONTDATAPATH . $fontkey . '.z');
                @unlink(_MPDF_TTFONTDATAPATH . $fontkey . '.cw127.php');
                @unlink(_MPDF_TTFONTDATAPATH . $fontkey . '.cw');
            } else {
                if ($this->debugfonts) {
                    $this->Error('Cannot write to the font caching directory - ' . _MPDF_TTFONTDATAPATH);
                }
            }
            unset($ttf);
        } else {
            $cw = @file_get_contents(_MPDF_TTFONTDATAPATH . $fontkey . '.cw.dat');
        }

        if (isset($this->fontdata[$family]['indic']) && $this->fontdata[$family]['indic']) {
            $indic = true;
        } else {
            $indic = false;
        }
        if (isset($this->fontdata[$family]['sip-ext']) && $this->fontdata[$family]['sip-ext']) {
            $sipext = $this->fontdata[$family]['sip-ext'];
        } else {
            $sipext = '';
        }


        $i = count($this->fonts) + $this->extraFontSubsets + 1;
        if ($sip || $smp) {
            $this->fonts[$fontkey] = array(
                'i'             => $i,
                'type'          => $type,
                'name'          => $name,
                'desc'          => $desc,
                'panose'        => $panose,
                'up'            => $up,
                'ut'            => $ut,
                'cw'            => $cw,
                'ttffile'       => $ttffile,
                'fontkey'       => $fontkey,
                'subsets'       => array(0 => range(0, 127)),
                'subsetfontids' => array($i),
                'used'          => false,
                'indic'         => $indic,
                'sip'           => $sip,
                'sipext'        => $sipext,
                'smp'           => $smp,
                'TTCfontID'     => $TTCfontID,
                'unAGlyphs'     => false,
            ); // mPDF 5.4.05
        } else {
            $ss = array();
            for ($s = 32; $s < 128; $s++) {
                $ss[$s] = $s;
            }
            $this->fonts[$fontkey] = array(
                'i'         => $i,
                'type'      => $type,
                'name'      => $name,
                'desc'      => $desc,
                'panose'    => $panose,
                'up'        => $up,
                'ut'        => $ut,
                'cw'        => $cw,
                'ttffile'   => $ttffile,
                'fontkey'   => $fontkey,
                'subset'    => $ss,
                'used'      => false,
                'indic'     => $indic,
                'sip'       => $sip,
                'sipext'    => $sipext,
                'smp'       => $smp,
                'TTCfontID' => $TTCfontID,
                'unAGlyphs' => $unAGlyphs,
            ); // mPDF 5.4.05
        }
        if ($this->useKerning && $haskerninfo) {
            $this->fonts[$fontkey]['kerninfo'] = $kerninfo;
        }
        $this->FontFiles[$fontkey] = array(
            'length1' => $originalsize,
            'type'    => "TTF",
            'ttffile' => $ttffile,
            'sip'     => $sip,
            'smp'     => $smp,
        );
        unset($cw);
    }
}

