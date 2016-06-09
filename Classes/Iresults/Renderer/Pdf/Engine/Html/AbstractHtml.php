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
use Iresults\Renderer\Exception\InvalidPathException;

/**
 * @author COD
 * Created 09.10.13 11:07
 */


abstract class AbstractHtml implements HtmlInterface {
	/**
	 * Drawing context
	 *
	 * @var MpdfHtml|object
	 */
	protected $context;

	/**
	 * Path the file will be saved at
	 *
	 * @var string
	 */
	protected $savePath = '';

	/**
	 * Path to the template file
	 *
	 * @var string
	 */
	protected $templatePath = '';

	/**
	 * The HTML template
	 *
	 * @var string
	 */
	protected $template = '';

	/**
	 * Path to the styles to be added to the PDF
	 *
	 * @var string
	 */
	protected $stylesPath = '';

	/**
	 * Styles for the PDF
	 *
	 * @var string
	 */
	protected $styles = '';

	/**
	 * Defines if the template needs to be rendered
	 * @var bool
	 */
	protected $_needsToRender = TRUE;

	/**
	 * Render the PDF
	 */
	abstract protected function _render();

	/**
	 * Initialize with the given template file path
	 *
	 * @param string $templatePath
	 * @return $this
	 */
	public function initWithTemplate($templatePath) {
		$this->templatePath = $templatePath;
		return $this;
	}

	/**
	 * Writes the rendered data to the given path
	 *
	 * @param string $savePath The path to which the output will be written
	 * @param string $type     The type of the writer
	 * @return void
	 */
	public function save($savePath = '', $type = NULL) {
		if (!$savePath) {
			$savePath = $this->getSavePath();
		}
		$this->render();
		$this->getContext()->Output($savePath, 'F');
	}

	/**
	 * Outputs the rendered data directly to the browser
	 *
	 * @param string $name This appears as the name of the downloaded file
	 * @param string $type The type of the writer
	 * @return void
	 */
	public function output($name = '', $type = NULL) {
		if (!$name) {
			$name = basename($this->getSavePath());
		}

		$this->sendHeaders($name, $type);

		$this->render();
		$this->getContext()->Output($name, 'D');
	}

	/**
	 * Render the template
	 *
	 * @return void
	 */
	public function render() {
		if ($this->_needsToRender) {
			$this->_render();
			$this->_needsToRender = FALSE;
		}
	}

	/**
	 * Returns the path the file will be saved at
	 *
	 * @return string
	 */
	public function getSavePath() {
		return $this->savePath;
	}

	/**
	 * Sets the path the file will be saved at
	 *
	 * @param String $savePath
	 * @return $this
	 */
	public function setSavePath($savePath) {
		$this->savePath = $savePath;
		return $this;
	}

	/**
	 * Set the current rendering context (i.e. a section or page)
	 *
	 * @param mixed $context
	 * @return $this
	 */
	public function setContext($context) {
		$this->_needsToRender = TRUE;
		$this->context = $context;
		return $this;
	}

	/**
	 * Returns the HTML template to be rendered
	 *
	 * @return string
	 */
	public function getTemplate() {
		if (!$this->template) {
			if ($this->templatePath) {
				return file_get_contents($this->templatePath);
			}
		}
		return $this->template;
	}

	/**
	 * Sets the HTML template to be rendered
	 *
	 * @param string $template
	 * @return $this
	 */
	public function setTemplate($template) {
		$this->_needsToRender = TRUE;
		$this->template = $template;
		return $this;
	}

	/**
	 * Sets the path to the HTML template to be rendered
	 *
	 * @param string $templatePath
	 * @return $this
     * @throws \Iresults\Renderer\Exception\InvalidPathException if the given path does not exist or is not readable
     */
	public function setTemplatePath($templatePath) {
		$this->_needsToRender = TRUE;

        if (!file_exists($templatePath)) {
            throw new InvalidPathException(
                sprintf('Template path "%s" could not be found', $templatePath),
                1429523757
            );
        }
        if (!is_readable($templatePath)) {
            throw new InvalidPathException(
                sprintf('Template path "%s" is not readable', $templatePath),
                1429523758
            );
        }

		$this->templatePath = $templatePath;
		return $this;
	}

	/**
	 * Returns the path to the HTML template to be rendered
	 *
	 * @return string
	 */
	public function getTemplatePath() {
		return $this->templatePath;
	}

	/**
	 * Adds the given styles to the PDF
	 *
	 * @param string $styles Either a file path or the styles as string
	 * @return $this
	 */
	public function setStyles($styles) {
		$this->_needsToRender = TRUE;

		$this->styles = $styles;
		if (file_exists($styles)) {
			$styles = file_get_contents($styles);
		}
		$this->getContext()->WriteHTML($styles, 1);
		return $this;
	}

	/**
	 * Returns the HTML template to be rendered
	 *
	 * @return string
	 */
	public function getStyles() {
		if (!$this->styles) {
			if ($this->stylesPath) {
				return file_get_contents($this->stylesPath);
			}
		}
		return $this->styles;
	}

    /**
     * Sets the path to the styles to be added to the PDF
     *
     * @param string $stylesPath
     * @return $this
     * @throws \Iresults\Renderer\Exception\InvalidPathException if the given path does not exist or is not readable
     */
	public function setStylesPath($stylesPath) {
		$this->_needsToRender = TRUE;

        if (!file_exists($stylesPath)) {
            throw new InvalidPathException(
                sprintf('Styles path "%s" could not be found', $stylesPath),
                1429523747
            );
        }
        if (!is_readable($stylesPath)) {
            throw new InvalidPathException(
                sprintf('Styles path "%s" is not readable', $stylesPath),
                1429523748
            );
        }
		$this->stylesPath = $stylesPath;
		return $this;
	}

	/**
	 * Returns the path to the styles to be added to the PDF
	 *
	 * @return string
	 */
	public function getStylesPath() {
		return $this->stylesPath;
	}

	/**
	 * Sends the headers for direct output of the rendered data.
	 *
	 * @param	string $name	This appears as the name of the downloaded file
	 * @param   string $type	Type for which to send the header
	 * @return	boolean Returns TRUE if the headers are successfully sent, else FALSE
	 */
	public function sendHeaders($name, $type = NULL) {
		// Will be sent by mPDF
	}
}