<?php
declare(strict_types=1);

namespace Iresults\Renderer\Pdf;

use Iresults\Renderer\AbstractRenderer;

abstract class AbstractPdfRenderer extends AbstractRenderer
{
    /**
     * @var string The orientation of the PDF pages.
     */
    protected $orientation = 'P';

    /**
     * @var string The unit the PDF pages are messured.
     */
    protected $unit = 'mm';

    /**
     * @var string The page format of the PDF.
     */
    protected $format = 'A4';

    /**
     * @var object The PDF object.
     */
    protected $pdf = null;

    /**
     * The constructor
     *
     * @param array $parameters
     * @return \Iresults\Renderer\Pdf\AbstractPdfRenderer
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);

        $this->setPropertiesFromArray($parameters);

        if (!$this->pdf) {
            $this->pdf = Factory::makeInstance();
        }

        return $this;
    }

    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* STATIC HELPER METHODS    WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Split the text by the occurrence of new line characters
     *
     * @param string $text The text to split
     * @return string[]    An array of split text parts, or an array containing the given text as it's only element
     */
    public static function splitText(string $text): array
    {
        if (strpos($text, "\r\n") !== false) {
            $textPieces = explode("\r\n", $text);
        } elseif (strpos($text, "\n") !== false) {
            $textPieces = explode("\n", $text);
        } elseif (strpos($text, "\r") !== false) {
            $textPieces = explode("\r", $text);
        } elseif (strpos($text, '\r\n') !== false) {
            $textPieces = explode('\r\n', $text);
        } elseif (strpos($text, '\r') !== false) {
            $textPieces = explode('\r', $text);
        } elseif (strpos($text, '\n') !== false) {
            $textPieces = explode('\n', $text);
        } else {
            $textPieces = [$text];
        }

        return $textPieces;
    }

    /**
     * Return the length of the longest part of the split input.
     *
     * If the input is a string it will be split using `self::splitText`.
     *
     * @param object       $that     The object that will respond to GetStringWidth() if the width of the string should be computed
     * @param string|array $input    The input to get the longest part of
     * @param string       $info     The information to fetch. Pass one of the following:
     *                               - 'width' fetches the width of the string according to the current font settings of the object passed in $that
     *                               - 'count' fetches the number of characters
     *                               - 'part' fetches and returns the longest part
     *                               - 'all' fetches all the information and returns it in an array
     * @return mixed    The result according to the passed $info-value
     */
    public static function getLongestPartOfSplitText(object $that, $input, string $info = 'width')
    {
        if (!is_array($input) || $input instanceof \Traversable) {
            $input = self::splitText($input);
        }

        $longest = '';
        $longestLength = 0;

        /**
         * Determine the longest part.
         */
        foreach ($input as $part) {
            if (strlen($part) > $longestLength) {
                $longest = $part;
                $longestLength = strlen($part);
            }
        }

        switch ($info) {
            case 'all':
                $result = [
                    'part'  => $longest,
                    'count' => $longestLength,
                    'width' => $that->GetStringWidth($longest),
                ];
                break;

            case 'part':
                $result = $longest;
                break;

            case 'count':
                $result = $longestLength;
                break;

            case 'length':
            case 'width':
            default:
                $result = $that->GetStringWidth($longest);
                break;
        }

        return $result;
    }
}
