<?php
declare(strict_types=1);

namespace Iresults\Renderer\Pdf\Wrapper\MpdfWrapper;

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
class V6 extends BaseMpdf implements MpdfWrapperInterface
{
    /**
     * A list of defaults which will be set after the mPDF initialization
     *
     * @var array
     */
    protected $overwriteDefaults = [
        'autoLangToFont' => false,
    ];

    /**
     * Paths to directories where fonts are stored in
     *
     * @var array
     */
    protected $fontDirectoryPaths = [
        _MPDF_TTFONTPATH,
    ];

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

        $isMinimumVersion6 = defined('mPDF_VERSION') && version_compare(mPDF_VERSION, '6.0') >= 0;
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
            call_user_func_array([$this, 'mPDF'], $funcArgs);
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
     * @return string[]
     */
    public function getFontDirectoryPaths(): array
    {
        return $this->fontDirectoryPaths;
    }

    /**
     * Sets the paths to directories where fonts are stored in
     *
     * @param string[] $fontDirectoryPaths
     * @return MpdfWrapperInterface
     */
    public function setFontDirectoryPaths(array $fontDirectoryPaths): MpdfWrapperInterface
    {
        $this->fontDirectoryPaths = $fontDirectoryPaths;

        return $this;
    }

    /**
     * Adds a directory where fonts are stored in
     *
     * @param string $fontDirectoryPath
     * @return MpdfWrapperInterface
     */
    public function addFontDirectoryPath(string $fontDirectoryPath): MpdfWrapperInterface
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
     * @return $this
     * @throws Exception if an entry in the collection is invalid
     * @throws InvalidFontNameException if the font name is not lower case
     */
    public function registerFonts(array $fontDataCollection): MpdfWrapperInterface
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
     * @return MpdfWrapperInterface
     * @throws InvalidFontNameException if the font name is not lower case
     */
    public function registerFont(string $fontName, array $fontData): MpdfWrapperInterface
    {
        if (strtolower($fontName) !== $fontName) {
            throw new InvalidFontNameException('Font name must be lower case', 1392652327);
        }

        return $this->registerFonts([$fontName => $fontData]);
    }

    /**
     * Validates the given font data
     *
     * @param array $fontData Font data to validate
     * @param array     <mixed> $error Reference to be filled with the error
     * @return boolean Returns if the data is valid
     */
    public function validateFontData(array $fontData, &$error = null): bool
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
    public function getPathForFont(string $fontFileName): ?string
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

        /* -- CJK-FONTS -- */
        if (in_array($family, $this->available_CJK_fonts)) {
            if (empty($this->Big5_widths)) {
                require(_MPDF_PATH . 'includes/CJKdata.php');
            }
            $this->AddCJKFont($family); // don't need to add style

            return;
        }
        /* -- END CJK-FONTS -- */

        if ($this->usingCoreFont) {
            throw new MpdfException("mPDF Error - problem with Font management");
        }

        $stylekey = $style;
        if (!$style) {
            $stylekey = 'R';
        }

        if (!isset($this->fontdata[$family][$stylekey]) || !$this->fontdata[$family][$stylekey]) {
            throw new MpdfException('mPDF Error - Font is not supported - ' . $family . ' ' . $style);
        }

        $name = '';
        $originalsize = 0;
        $sip = false;
        $smp = false;
        $useOTL = 0; // mPDF 5.7.1
        $fontmetrics = ''; // mPDF 6
        $haskerninfo = false;
        $haskernGPOS = false;
        $hassmallcapsGSUB = false;
        $BMPselected = false;
        $GSUBScriptLang = [];
        $GSUBFeatures = [];
        $GSUBLookups = [];
        $GPOSScriptLang = [];
        $GPOSFeatures = [];
        $GPOSLookups = [];
        if (file_exists(_MPDF_TTFONTDATAPATH . $fontkey . '.mtx.php')) {
            include(_MPDF_TTFONTDATAPATH . $fontkey . '.mtx.php');
        }

        $ttffile = '';
        if (defined('_MPDF_SYSTEM_TTFONTS')) {
            $ttffile = _MPDF_SYSTEM_TTFONTS . $this->fontdata[$family][$stylekey];
            if (!file_exists($ttffile)) {
                $ttffile = '';
            }
        }
        if (!$ttffile) {
            $ttffile = $this->getPathForFont($this->fontdata[$family][$stylekey]);
            if (!file_exists($ttffile)) {
                throw new MpdfException("mPDF Error - cannot find TTF TrueType font file - " . $ttffile);
            }
        }
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
        } elseif (!$BMPonly && $BMPselected) {
            $regenerate = true;
        }
        // mPDF 5.7.1
        if (isset($this->fontdata[$family]['useOTL']) && $this->fontdata[$family]['useOTL'] && $useOTL != $this->fontdata[$family]['useOTL']) {
            $regenerate = true;
            $useOTL = $this->fontdata[$family]['useOTL'];
        } elseif ((!isset($this->fontdata[$family]['useOTL']) || !$this->fontdata[$family]['useOTL']) && $useOTL) {
            $regenerate = true;
            $useOTL = 0;
        }
        if (_FONT_DESCRIPTOR != $fontmetrics) {
            $regenerate = true;
        } // mPDF 6
        if (!isset($name) || $originalsize != $ttfstat['size'] || $regenerate) {
            if (!class_exists('TTFontFile', false)) {
                include(_MPDF_PATH . 'classes/ttfontsuni.php');
            }
            $ttf = new TTFontFile();
            $ttf->getMetrics($ttffile, $fontkey, $TTCfontID, $this->debugfonts, $BMPonly, $useOTL); // mPDF 5.7.1
            $cw = $ttf->charWidths;
            $kerninfo = $ttf->kerninfo;
            if ($kerninfo) {
                $haskerninfo = true;
            }
            $haskernGPOS = $ttf->haskernGPOS;
            $hassmallcapsGSUB = $ttf->hassmallcapsGSUB;
            $name = preg_replace('/[ ()]/', '', $ttf->fullName);
            $sip = $ttf->sipset;
            $smp = $ttf->smpset;
            // mPDF 6
            $GSUBScriptLang = $ttf->GSUBScriptLang;
            $GSUBFeatures = $ttf->GSUBFeatures;
            $GSUBLookups = $ttf->GSUBLookups;
            $rtlPUAstr = $ttf->rtlPUAstr;
            $GPOSScriptLang = $ttf->GPOSScriptLang;
            $GPOSFeatures = $ttf->GPOSFeatures;
            $GPOSLookups = $ttf->GPOSLookups;
            $glyphIDtoUni = $ttf->glyphIDtoUni;

            $desc = [
                'CapHeight'    => round($ttf->capHeight),
                'XHeight'      => round($ttf->xHeight),
                'FontBBox'     => '[' . round($ttf->bbox[0]) . " " . round($ttf->bbox[1]) . " " . round(
                        $ttf->bbox[2]
                    ) . " " . round($ttf->bbox[3]) . ']',
                /* FontBBox from head table */

                /* 		'MaxWidth' => round($ttf->advanceWidthMax),	// AdvanceWidthMax from hhea table	NB ArialUnicode MS = 31990 ! */
                'Flags'        => $ttf->flags,
                'Ascent'       => round($ttf->ascent),
                'Descent'      => round($ttf->descent),
                'Leading'      => round($ttf->lineGap),
                'ItalicAngle'  => $ttf->italicAngle,
                'StemV'        => round($ttf->stemV),
                'MissingWidth' => round($ttf->defaultWidth),
            ];
            $panose = '';
            if (count($ttf->panose)) {
                $panoseArray = array_merge([$ttf->sFamilyClass, $ttf->sFamilySubClass], $ttf->panose);
                foreach ($panoseArray as $value) {
                    $panose .= ' ' . dechex($value);
                }
            }
            $unitsPerEm = round($ttf->unitsPerEm);
            $up = round($ttf->underlinePosition);
            $ut = round($ttf->underlineThickness);
            $strp = round($ttf->strikeoutPosition); // mPDF 6
            $strs = round($ttf->strikeoutSize); // mPDF 6
            $originalsize = $ttfstat['size'] + 0;
            $type = 'TTF';
            //Generate metrics .php file
            $s = '<?php' . "\n";
            $s .= '$name=\'' . $name . "';\n";
            $s .= '$type=\'' . $type . "';\n";
            $s .= '$desc=' . var_export($desc, true) . ";\n";
            $s .= '$unitsPerEm=' . $unitsPerEm . ";\n";
            $s .= '$up=' . $up . ";\n";
            $s .= '$ut=' . $ut . ";\n";
            $s .= '$strp=' . $strp . ";\n"; // mPDF 6
            $s .= '$strs=' . $strs . ";\n"; // mPDF 6
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
            if ($haskerninfo) {
                $s .= '$haskerninfo=true;' . "\n";
            } else {
                $s .= '$haskerninfo=false;' . "\n";
            }
            if ($haskernGPOS) {
                $s .= '$haskernGPOS=true;' . "\n";
            } else {
                $s .= '$haskernGPOS=false;' . "\n";
            }
            if ($hassmallcapsGSUB) {
                $s .= '$hassmallcapsGSUB=true;' . "\n";
            } else {
                $s .= '$hassmallcapsGSUB=false;' . "\n";
            }
            $s .= '$fontmetrics=\'' . _FONT_DESCRIPTOR . "';\n"; // mPDF 6

            $s .= '// TypoAscender/TypoDescender/TypoLineGap = ' . round($ttf->typoAscender) . ', ' . round(
                    $ttf->typoDescender
                ) . ', ' . round($ttf->typoLineGap) . "\n";
            $s .= '// usWinAscent/usWinDescent = ' . round($ttf->usWinAscent) . ', ' . round(
                    -$ttf->usWinDescent
                ) . "\n";
            $s .= '// hhea Ascent/Descent/LineGap = ' . round($ttf->hheaascent) . ', ' . round(
                    $ttf->hheadescent
                ) . ', ' . round($ttf->hhealineGap) . "\n";

            //  mPDF 5.7.1
            if (isset($this->fontdata[$family]['useOTL'])) {
                $s .= '$useOTL=' . $this->fontdata[$family]['useOTL'] . ';' . "\n";
            } else {
                $s .= '$useOTL=0x0000;' . "\n";
            }
            if ($rtlPUAstr) {
                $s .= '$rtlPUAstr=\'' . $rtlPUAstr . "';\n";
            } else {
                $s .= '$rtlPUAstr=\'\';' . "\n";
            }
            if (count($GSUBScriptLang)) {
                $s .= '$GSUBScriptLang=' . var_export($GSUBScriptLang, true) . ";\n";
            }
            if (count($GSUBFeatures)) {
                $s .= '$GSUBFeatures=' . var_export($GSUBFeatures, true) . ";\n";
            }
            if (count($GSUBLookups)) {
                $s .= '$GSUBLookups=' . var_export($GSUBLookups, true) . ";\n";
            }
            if (count($GPOSScriptLang)) {
                $s .= '$GPOSScriptLang=' . var_export($GPOSScriptLang, true) . ";\n";
            }
            if (count($GPOSFeatures)) {
                $s .= '$GPOSFeatures=' . var_export($GPOSFeatures, true) . ";\n";
            }
            if (count($GPOSLookups)) {
                $s .= '$GPOSLookups=' . var_export($GPOSLookups, true) . ";\n";
            }
            if ($kerninfo) {
                $s .= '$kerninfo=' . var_export($kerninfo, true) . ";\n";
            }
            $s .= "?>";
            if (is_writable(dirname(_MPDF_TTFONTDATAPATH . 'x'))) {
                $fh = fopen(_MPDF_TTFONTDATAPATH . $fontkey . '.mtx.php', "w");
                fwrite($fh, $s, strlen($s));
                fclose($fh);
                $fh = fopen(_MPDF_TTFONTDATAPATH . $fontkey . '.cw.dat', "wb");
                fwrite($fh, $cw, strlen($cw));
                fclose($fh);
                // mPDF 5.7.1
                $fh = fopen(_MPDF_TTFONTDATAPATH . $fontkey . '.gid.dat', "wb");
                fwrite($fh, $glyphIDtoUni, strlen($glyphIDtoUni));
                fclose($fh);
                if (file_exists(_MPDF_TTFONTDATAPATH . $fontkey . '.cgm')) {
                    unlink(_MPDF_TTFONTDATAPATH . $fontkey . '.cgm');
                }
                if (file_exists(_MPDF_TTFONTDATAPATH . $fontkey . '.z')) {
                    unlink(_MPDF_TTFONTDATAPATH . $fontkey . '.z');
                }
                if (file_exists(_MPDF_TTFONTDATAPATH . $fontkey . '.cw127.php')) {
                    unlink(_MPDF_TTFONTDATAPATH . $fontkey . '.cw127.php');
                }
                if (file_exists(_MPDF_TTFONTDATAPATH . $fontkey . '.cw')) {
                    unlink(_MPDF_TTFONTDATAPATH . $fontkey . '.cw');
                }
            } elseif ($this->debugfonts) {
                throw new MpdfException('Cannot write to the font caching directory - ' . _MPDF_TTFONTDATAPATH);
            }
            unset($ttf);
        } else {
            $cw = '';
            $glyphIDtoUni = '';
            if (file_exists(_MPDF_TTFONTDATAPATH . $fontkey . '.cw.dat')) {
                $cw = file_get_contents(_MPDF_TTFONTDATAPATH . $fontkey . '.cw.dat');
            }
            if (file_exists(_MPDF_TTFONTDATAPATH . $fontkey . '.gid.dat')) {
                $glyphIDtoUni = file_get_contents(_MPDF_TTFONTDATAPATH . $fontkey . '.gid.dat');
            }
        }

        /* -- OTL -- */
        // mPDF 5.7.1
        // Use OTL OpenType Table Layout - GSUB
        if (isset($this->fontdata[$family]['useOTL']) && ($this->fontdata[$family]['useOTL'])) {
            if (!class_exists('otl', false)) {
                include(_MPDF_PATH . 'classes/otl.php');
            }
            if (empty($this->otl)) {
                $this->otl = new \otl($this);
            }
        }
        /* -- END OTL -- */

        if (isset($this->fontdata[$family]['sip-ext']) && $this->fontdata[$family]['sip-ext']) {
            $sipext = $this->fontdata[$family]['sip-ext'];
        } else {
            $sipext = '';
        }

        // Override with values from config_font.php
        if (isset($this->fontdata[$family]['Ascent']) && $this->fontdata[$family]['Ascent']) {
            $desc['Ascent'] = $this->fontdata[$family]['Ascent'];
        }
        if (isset($this->fontdata[$family]['Descent']) && $this->fontdata[$family]['Descent']) {
            $desc['Descent'] = $this->fontdata[$family]['Descent'];
        }
        if (isset($this->fontdata[$family]['Leading']) && $this->fontdata[$family]['Leading']) {
            $desc['Leading'] = $this->fontdata[$family]['Leading'];
        }

        $i = count($this->fonts) + $this->extraFontSubsets + 1;
        if ($sip || $smp) {
            $this->fonts[$fontkey] = [
                'i'                => $i,
                'type'             => $type,
                'name'             => $name,
                'desc'             => $desc,
                'panose'           => $panose,
                'unitsPerEm'       => $unitsPerEm,
                'up'               => $up,
                'ut'               => $ut,
                'strs'             => $strs,
                'strp'             => $strp,
                'cw'               => $cw,
                'ttffile'          => $ttffile,
                'fontkey'          => $fontkey,
                'subsets'          => [0 => range(0, 127)],
                'subsetfontids'    => [$i],
                'used'             => false,
                'sip'              => $sip,
                'sipext'           => $sipext,
                'smp'              => $smp,
                'TTCfontID'        => $TTCfontID,
                'useOTL'           => (isset($this->fontdata[$family]['useOTL']) ? $this->fontdata[$family]['useOTL'] : false),
                'useKashida'       => (isset($this->fontdata[$family]['useKashida']) ? $this->fontdata[$family]['useKashida'] : false),
                'GSUBScriptLang'   => $GSUBScriptLang,
                'GSUBFeatures'     => $GSUBFeatures,
                'GSUBLookups'      => $GSUBLookups,
                'GPOSScriptLang'   => $GPOSScriptLang,
                'GPOSFeatures'     => $GPOSFeatures,
                'GPOSLookups'      => $GPOSLookups,
                'rtlPUAstr'        => $rtlPUAstr,
                'glyphIDtoUni'     => $glyphIDtoUni,
                'haskerninfo'      => $haskerninfo,
                'haskernGPOS'      => $haskernGPOS,
                'hassmallcapsGSUB' => $hassmallcapsGSUB,
            ]; // mPDF 5.7.1	// mPDF 6
        } else {
            $ss = [];
            for ($s = 32; $s < 128; $s++) {
                $ss[$s] = $s;
            }
            $this->fonts[$fontkey] = [
                'i'                => $i,
                'type'             => $type,
                'name'             => $name,
                'desc'             => $desc,
                'panose'           => $panose,
                'unitsPerEm'       => $unitsPerEm,
                'up'               => $up,
                'ut'               => $ut,
                'strs'             => $strs,
                'strp'             => $strp,
                'cw'               => $cw,
                'ttffile'          => $ttffile,
                'fontkey'          => $fontkey,
                'subset'           => $ss,
                'used'             => false,
                'sip'              => $sip,
                'sipext'           => $sipext,
                'smp'              => $smp,
                'TTCfontID'        => $TTCfontID,
                'useOTL'           => (isset($this->fontdata[$family]['useOTL']) ? $this->fontdata[$family]['useOTL'] : false),
                'useKashida'       => (isset($this->fontdata[$family]['useKashida']) ? $this->fontdata[$family]['useKashida'] : false),
                'GSUBScriptLang'   => $GSUBScriptLang,
                'GSUBFeatures'     => $GSUBFeatures,
                'GSUBLookups'      => $GSUBLookups,
                'GPOSScriptLang'   => $GPOSScriptLang,
                'GPOSFeatures'     => $GPOSFeatures,
                'GPOSLookups'      => $GPOSLookups,
                'rtlPUAstr'        => $rtlPUAstr,
                'glyphIDtoUni'     => $glyphIDtoUni,
                'haskerninfo'      => $haskerninfo,
                'haskernGPOS'      => $haskernGPOS,
                'hassmallcapsGSUB' => $hassmallcapsGSUB,
            ]; // mPDF 5.7.1	// mPDF 6
        }
        if ($haskerninfo) {
            $this->fonts[$fontkey]['kerninfo'] = $kerninfo;
        }
        $this->FontFiles[$fontkey] = [
            'length1' => $originalsize,
            'type'    => "TTF",
            'ttffile' => $ttffile,
            'sip'     => $sip,
            'smp'     => $smp,
        ];
        unset($cw);
    }
}

