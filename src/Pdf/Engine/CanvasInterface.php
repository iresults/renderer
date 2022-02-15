<?php
namespace Iresults\Renderer\Pdf\Engine;

    /***************************************************************
     *  Copyright notice
     *
     * (c) 2010 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
     *            Daniel Corn <cod@iresults.li>, iresults
     *  All rights reserved
     *
     *  This script is part of the TYPO3 project. The TYPO3 project is
     *  free software; you can redistribute it and/or modify
     *  it under the terms of the GNU General Public License as published by
     *  the Free Software Foundation; either version 2 of the License, or
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
     ***************************************************************/


/**
 * The interface for all canvas PDF rendering classes.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Core
 */
interface CanvasInterface
{
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* STYLE SETTER INTERFACE     WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Set the fill style.
     *
     * @param    string $value
     */
    public function fillStyle($value);

    /**
     * Set the stroke style. Currently only RGB color settings are allowed.
     *
     * @param    string $value
     */
    public function strokeStyle($value);

    /**
     * Set the width of the stroke of the next stroke() call.
     *
     * @param    float $width
     */
    public function lineWidth($width);



    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* DRAWING & TEXT RENDERING   WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Draw the given text from the starting point ($x,$y). Provide an width the text should be
     * aligned to, so that the text will be aligned right to the point at $x+$widthToAlignRight
     *
     * @param    string $text             The text to be drawn. If the property $autoTranslateHelper is set this text will be translated
     * @param    float  $x                .0 The text's x offset
     * @param    float  $y                .0 The text's y offset
     * @param    string $alignRight       May be set to left (='L'), right (='R') or centered (='C')
     * @param    mixed  $translationPara1 Parameter to be passed to the translation function
     * @param    mixed  $translationPara2 Parameter to be passed to the translation function
     * @param    mixed  $translationPara3 Parameter to be passed to the translation function
     */
    public function fillText(
        $text,
        $x = 0.0,
        $y = 0.0,
        $align = 'L',
        $translationPara1 = null,
        $translationPara2 = null,
        $translationPara3 = null
    );

    /**
     * Draw an image at the specified position on the page.
     *
     * @param    string|resource $image  The absolute path to the image or a matching image ressource
     * @param    float           $x      The image's x offset
     * @param    float           $y      The image's y offset
     * @param    float           $width  The image's width
     * @param    float           $height The image's height
     * @return    void
     */
    public function drawImage($image, $x = -1, $y = -1, $width = -1, $height = -1);

    /**
     * Draw a line to the point ($x,$y).
     *
     * @param    float $x
     * @param    float $y
     */
    public function lineTo($x, $y);

    /**
     * Draws the stroke of the created path.
     */
    public function stroke();

    /**
     * Fills the created path.
     */
    public function fill();

    /**
     * Begins a new path.
     */
    public function beginPath();

    /**
     * Closes a started path.
     */
    public function closePath();

    /**
     * Move the current point to the given position.
     *
     * @param    float $newX
     * @param    float $newY
     */
    public function moveTo($newX, $newY);

    /**
     * Save the current point (its x and y positions).
     */
    public function saveContext();

    /**
     * Restores the saved position.
     */
    public function restoreContext();



    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* FONTS             MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Set the font to use to the described font. The parameter has to be in the format
     * "8.0px 'Helvetica'" or "Bold 8.0px 'Verdana'"
     *
     * @param    string $fontText
     */
    public function font($fontText);

    /**
     * @see Zend_Pdf_Page::setFont()
     */
    #public function setFont($font,$fontSize);

    /**
     * Set the line height of multiline text. The font line height is applied as a factor.
     *
     * @param    float $fontLineHeight
     */
    public function setFontLineHeight($fontLineHeight);

    /**
     * Returns the font line height.
     *
     * @return    float
     */
    public function getFontLineHeight();



    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* INTERNAL          MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Applies a canvas to PDF scale value.
     *
     * @param    float $input
     * @return    float
     */
    public function _applyCanvasToPdfScale($input);

    /**
     * Applies a PDF to canvas scale value.
     *
     * @param    float $input
     * @return    float
     */
    public function _applyPdfToCanvasScale($input);

    /**
     * Translates the given y value into the flipped PDF value.
     *
     * @param    float $y
     * @return    float
     */
    public function _yToCoordinateSystem($y);

    /**
     * Calculate the new x value for the text aligned right.
     *
     *     originalX + widthToAlignRight------------------------------
     *
     *     ---------------------->alignedX -------------------textWidth
     *
     * @param    string $text
     * @param    float  $originalX
     * @param    float  $widthToAlignRight
     * @return    float
     */
    public function _getXAlignedRightToWidth($text, $originalX, $widthToAlignRight);

    /**
     * Calculate the width of a given text in the current font and font-size.
     *
     * @param    string $text
     * @return    float
     */
    public function _getTextWidth($text);



    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* ACCESSOR METHOD TEMPLATES   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * This is a template for the magic setter overwrite:
     * Setter overwrite for keywords like "font", "fillStyle" and "lineWidth"
     *
     * @param    string $name
     * @param    mixed  $value
     */
    /*	public function __set($name,$value) {
            if ($name == 'fillStyle') {
                $this->fillStyle($value);
            } elseif ($name == 'font') {
                $this->font($value);
            } elseif ($name == 'lineWidth') {
                $this->lineWidth($value);
            } elseif ($name == 'strokeStyle') {
                $this->strokeStyle($value);
            } elseif ($name == 'fontLineHeight') {
                $this->setFontLineHeight($value);
            } else {
                $this->$name = $value;
            }
        }
    /* */

    /**
     * This is a template for the magic getter overwrite:
     * Getter overwrite for keywords like "fontLineHeight".
     *
     * @param    string $name
     * @return    mixed
     */
    /*	public function __get($name) {
            if ($name == 'fontLineHeight') {
                return $this->getFontLineHeight();
            } else {
                return $this->$name;
            }
        }
    /* */
}