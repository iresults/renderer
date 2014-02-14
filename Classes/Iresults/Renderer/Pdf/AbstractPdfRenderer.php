<?php
namespace Iresults\Renderer\Pdf;

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

use Iresults\Renderer\AbstractRenderer;

/**
 * The iresults addition to the FPDF library.
 * It includes a delegate to output the header and footer. The delegate methods
 * pdfHeader() and pdfFooter() are invoked automatically if a new page is insert.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Pdf
 */
abstract class AbstractPdfRenderer extends AbstractRenderer {
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
	protected $pdf = NULL;

	/**
	 * The constructor
	 *
	 * @param    array $parameters
	 * @return \Iresults\Renderer\Pdf\AbstractPdfRenderer
	 */
	public function __construct(array $parameters = array()) {
		parent::__construct($parameters);

		$this->setPropertiesFromArray($parameters);

		if (!$this->pdf) {
			$this->pdf = Factory::makeInstance();
		}
		return $this;
	}

	/**
	 * @see file://localhost/Volumes/Daten/99_htdocs/typo3/devwebs/devweb.intern/typo3conf/ext/iresults/Classes/Renderer/Abstract.php::sendHeaders()
	 */
//	public function sendHeaders() {
//		if (!headers_sent()) {
//			header('Content-Type: application/pdf');
//
//			// TODO: Check if the browser is IE
//			#header("Content-Disposition: attachment; filename=$name".time().".pdf");
//
//			if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
//				$expires = 60*60*24*14;
//				header('Pragma: public');
//				header('Cache-Control: maxage=' . $expires);
//				header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
//			} else {
//				header('Cache-Control: no-cache, must-revalidate');
//			}
//			return TRUE;
//		} else {
//			return FALSE;
//		}
//	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* STATIC HELPER METHODS    WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Splits the text by the occurrence of new line characters.
	 *
	 * @param    string $text The text to split
	 * @return    array<string>    An array of split text parts, or an array containing the given text as it's only element
	 */
	static public function splitText($text) {
		if (strpos($text, "\r\n") !== FALSE) {
			$textPieces = explode("\r\n", $text);
		} else if (strpos($text, "\n") !== FALSE) {
			$textPieces = explode("\n", $text);
		} else if (strpos($text, "\r") !== FALSE) {
			$textPieces = explode("\r", $text);
		} else if (strpos($text, '\r\n') !== FALSE) {
			$textPieces = explode('\r\n', $text);
		} else if (strpos($text, '\r') !== FALSE) {
			$textPieces = explode('\r', $text);
		} else if (strpos($text, '\n') !== FALSE) {
			$textPieces = explode('\n', $text);
		} else {
			$textPieces = array($text);
		}
		return $textPieces;
	}


	/**
	 * Returns the length of the longest part of the split input.
	 *
	 * If the input is a string it will be split using the _splitText-method.
	 *
	 * @param    object       $that  The object that will respond to GetStringWidth() if the width of the string should be computed
	 * @param    string|array $input The input to get the longest part of
	 * @param    string       $info  The information to fetch. Pass one of the following:
	 *                               - 'width' fetches the width of the string according to the current font settings of the object passed in $that
	 *                               - 'count' fetches the number of characters
	 *                               - 'part' fetches and returns the longest part
	 *                               - 'all' fetches all the information and returns it in an array
	 * @return    mixed    The result according to the passed $info-value
	 */
	static public function getLongestPartOfSplitText($that, $input, $info = 'width') {
		if (!is_array($input) || $input instanceof \Traversable) {
			$input = self::splitText($input);
		}

		$longest = '';
		$longestLength = 0;
		$result = FALSE;

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
				$result = array(
					'part' => $longest,
					'count' => $longestLength,
					'width' => $that->GetStringWidth($longest),
				);
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