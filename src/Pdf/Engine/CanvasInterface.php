<?php

declare(strict_types=1);

namespace Iresults\Renderer\Pdf\Engine;

/**
 * Interface for all canvas PDF rendering classes
 */
interface CanvasInterface
{
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* STYLE SETTER INTERFACE     WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Set the fill style
     */
    public function fillStyle(string $value);

    /**
     * Set the stroke style
     *
     * Currently, only RGB color settings are allowed.
     */
    public function strokeStyle(string $value);

    /**
     * Set the width of the stroke of the next stroke() call
     */
    public function lineWidth(float $width);

    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* DRAWING & TEXT RENDERING   WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Draw the given text from the starting point ($x,$y). Provide an width the text should be
     * aligned to, so that the text will be aligned right to the point at $x+$widthToAlignRight
     *
     * @param string $text             The text to be drawn. If the property $autoTranslateHelper is set this text will be translated
     * @param float  $x                The text's x offset
     * @param float  $y                The text's y offset
     * @param string $align            May be set to left (='L'), right (='R') or centered (='C')
     * @param mixed  $translationPara1 Parameter to be passed to the translation function
     * @param mixed  $translationPara2 Parameter to be passed to the translation function
     * @param mixed  $translationPara3 Parameter to be passed to the translation function
     */
    public function fillText(
        string $text,
        float $x = 0.0,
        float $y = 0.0,
        string $align = 'L',
        $translationPara1 = null,
        $translationPara2 = null,
        $translationPara3 = null,
    );

    /**
     * Draw an image at the specified position on the page
     *
     * @param string $image  The absolute path to the image or a matching image ressource
     * @param float  $x      The image's x offset
     * @param float  $y      The image's y offset
     * @param float  $width  The image's width
     * @param float  $height The image's height
     *
     * @return void
     */
    public function drawImage(
        string $image,
        float $x = -1.0,
        float $y = -1.0,
        float $width = -1.0,
        float $height = -1.0,
    );

    /**
     * Draw a line to the point ($x,$y)
     */
    public function lineTo(float $x, float $y);

    /**
     * Draw the stroke of the created path
     */
    public function stroke();

    /**
     * Fill the created path
     */
    public function fill();

    /**
     * Begin a new path
     */
    public function beginPath();

    /**
     * Close a started path
     */
    public function closePath();

    /**
     * Move the current point to the given position
     */
    public function moveTo(float $newX, float $newY);

    /**
     * Save the current point (its x and y positions)
     */
    public function saveContext();

    /**
     * Restore the saved position
     *
     * @see saveContext()
     */
    public function restoreContext();

    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* FONTS             MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Set the font to use to the described font
     *
     * The parameter has to be in the format "8.0px 'Helvetica'" or "Bold 8.0px 'Verdana'"
     */
    public function font(string $fontText);

    /**
     * Set the line height of multiline text
     *
     * The font line height is applied as a factor.
     */
    public function setFontLineHeight(float $fontLineHeight);

    /**
     * Return the font line height
     */
    public function getFontLineHeight(): float;

    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* INTERNAL          MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Apply a canvas to PDF scale value
     */
    public function _applyCanvasToPdfScale(float $input): float;

    /**
     * Apply a PDF to canvas scale value
     */
    public function _applyPdfToCanvasScale(float $input): float;

    /**
     * Translate the given y value into the flipped PDF value
     */
    public function _yToCoordinateSystem(float $y): float;

    /**
     * Calculate the new x value for the text aligned right
     *
     *     originalX + widthToAlignRight------------------------------
     *
     *     ---------------------->alignedX -------------------textWidth
     */
    public function _getXAlignedRightToWidth(string $text, float $originalX, float $widthToAlignRight): float;

    /**
     * Calculate the width of a given text in the current font and font-size
     */
    public function _getTextWidth(string $text): float;
}
