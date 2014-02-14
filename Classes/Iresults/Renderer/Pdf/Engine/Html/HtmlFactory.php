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

/**
 * @author COD
 * Created 09.10.13 10:40
 */
use Iresults\Renderer\Helpers\AbstractFactory;

/**
 * Factory for HTML PDF engines
 */
class HtmlFactory extends AbstractFactory {
	/**
	 * Returns a new canvas renderer
	 *
	 * @param array $constructorArguments Optional arguments to pass to the constructor
	 * @return HtmlInterface
	 */
	static public function renderer($constructorArguments = array()) {
		return static::_createInstance($constructorArguments);
	}

	/**
	 * Returns the name of the class the factory should produce
	 *
	 * @return string
	 */
	static protected function _getFactoryClass() {
		if (class_exists('mPDF')) {
			return 'Tx_Iresults_Renderer_Pdf_Engine_Html_Mpdf';
		}
		return FALSE;
	}


	/**
	 * Returns a new canvas renderer with the given template
	 *
	 * @param string $template
	 * @return HtmlInterface
	 */
	static public function rendererWithTemplate($template) {
		/** @var HtmlInterface $instance */
		$instance = static::renderer();
		return $instance->initWithTemplate($template);
	}
}