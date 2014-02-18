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
 * Created 09.10.13 10:37
 */
use Iresults\Renderer\RendererInterface;

/**
 * Interface for the HTML PDF engine
 */
interface HtmlInterface extends RendererInterface {
	/**
	 * Initialize with the given template file path
	 *
	 * @param string $templatePath
	 * @return $this
	 */
	public function initWithTemplate($templatePath);

	/**
	 * Returns the HTML template to be rendered
	 *
	 * @return string
	 */
	public function getTemplate();

	/**
	 * Sets the HTML template to be rendered
	 *
	 * @param string $template
	 */
	public function setTemplate($template);

	/**
	 * Sets the path to the HTML template to be rendered
	 *
	 * Note: The template path has a higher priority than the template property
	 *
	 * @param string $templatePath
	 * @return $this
	 */
	public function setTemplatePath($templatePath);

	/**
	 * Returns the path to the HTML template to be rendered
	 *
	 * Note: The template path has a higher priority than the template property
	 *
	 * @return string
	 */
	public function getTemplatePath();

	/**
	 * Adds the given styles to the PDF
	 *
	 * @param string $styles Either a file path or the styles as string
	 * @return $this
	 */
	public function setStyles($styles);

	/**
	 * Returns the HTML template to be rendered
	 *
	 * @return string
	 */
	public function getStyles();

	/**
	 * Sets the path to the styles to be added to the PDF
	 *
	 * Note: The style path has a higher priority than the styles property
	 *
	 * @param string $stylesPath
	 * @return $this
	 */
	public function setStylesPath($stylesPath);

	/**
	 * Returns the path to the styles to be added to the PDF
	 *
	 * Note: The style path has a higher priority than the styles property
	 *
	 * @return string
	 */
	public function getStylesPath();

	/**
	 * Renders the template
	 *
	 * @return void
	 */
	public function render();
}