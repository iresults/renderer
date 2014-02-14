<?php
namespace Iresults\Renderer\Pdf\Engine\Html;

/*
 *  Copyright notice
 *
 *  (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
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
use Iresults\Renderer\Pdf\Wrapper\Mpdf as BaseMpdf;

/**
 * @author COD
 * Created 09.10.13 10:43
 */

class Mpdf extends AbstractHtml {
	/**
	 * Render the PDF
	 */
	protected function _render() {
		$this->getContext()->WriteHTML($this->getStyles(), 1);
		$this->getContext()->WriteHTML($this->getTemplate(), 2);
	}

	/**
	 * Returns the current rendering context (i.e. a section or page)
	 *
	 * @return mixed
	 */
	public function getContext() {
		if (!$this->context) {
			$this->context = new BaseMpdf();
			$this->context->SetDisplayMode('fullpage');
			$this->context->list_indent_first_level = 0;    // 1 or 0 - whether to indent the first level of a list
		}
		return $this->context;
	}
}