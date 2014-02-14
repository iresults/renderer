<?php
namespace Iresults\Renderer\Pdf\Engine;

/***************************************************************
*  Copyright notice
*
 * (c) 2010 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *  			Daniel Corn <cod@iresults.li>, iresults
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
 * The interface describes the methods called on the delegate of a canvas engine.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Renderer_Pdf_Canvas
 */
interface CanvasDelegateInterface {
	/**
	 * Invoked when no template was found in the original class.
	 * You may return a template script content to render.
	 *
	 * @return	string The template script to render
	 */
	public function getTemplate();

	/**
	 * Invoked before the scripts are drawn.
	 *
	 * @return	void
	 */
	public function willDraw();

	/**
	 * Invoked at the end of the draw script.
	 * @return	void
	 */
	public function didDraw();

	/**
	 * Invoked before the template script is loaded.
	 *
	 * @return	void
	 */
	public function willRender();

	/**
	 * Invoked when the PDF did render.
	 *
	 * @return	void
	 */
	public function didRender();

	/**
	 * Invoked when the header should be rendered and no header script is found
	 * in the template.
	 *
	 * @return	void
	 */
	public function header();

	/**
	 * Invoked when the footer should be rendered and no footer script is found
	 * in the template.
	 *
	 * @return	void
	 */
	public function footer();
}