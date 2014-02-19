<?php
namespace Iresults\Renderer\Word;

/*
 * Copyright (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *                    Daniel Corn <cod@iresults.li>, iresults
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @copyright  Copyright (c) 2013
 * @license    http://opensource.org/licenses/MIT MIT
 * @version    1.0.0
 */

use Iresults\Renderer\AbstractRenderer as AbstractRenderer;

/**
 * The Word renderer
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults\Word
 */
class Renderer extends AbstractRenderer {
	/**
	 * The default writer type
	 * @var string
	 */
	protected $defaultWriterType = 'Word2007';

	/**
	 * Creates and prepares the driver instance
	 * @return \PHPWord Returns the driver
	 */
	public function initializeDriver() {
		$this->driver = new \PHPWord();
		$this->context = $this->driver->createSection();
	}

	/**
	 * Returns a new writer instance
	 * @param	string $type The type of the writer
	 * @return	\PHPWord_Writer_IWriter
	 */
	public function createWriter($type = NULL) {
		if (!$type) {
			$type = $this->defaultWriterType;
		}
		return \PHPWord_IOFactory::createWriter($this->getDriver(), $type);
	}

	/**
	 * Sends the headers for direct output of the rendered data.
	 *
	 * @param	string $name	This appears as the name of the downloaded file
	 * @param   string $type	Type for which to send the header
	 * @return	boolean Returns TRUE if the headers are successfully sent, else FALSE
	 */
	public function sendHeaders($name, $type = NULL) {
		if (!$type) {
			$type = $this->defaultWriterType;
		}
	}

	/**
	 * Initialize a new instance with the given template file path
	 * @param  string $templateFilePath
	 * @return AbstractRenderer
	 */
	public function initWithTemplate($templateFilePath) {
		$templateFilePath = \Iresults\Core\Iresults::getPathOfResource($templateFilePath);
		if (!is_readable($templateFilePath)) {
			throw new \UnexpectedValueException('Template file "' . $templateFilePath . '" is not readable', 1360939616);
		}
		$templateDriver = new Template($templateFilePath);
		$this->driver = $templateDriver;
		$this->context = $templateDriver;
		return $this;
	}


	/**
	 * Writes the rendered data to the given path.
	 *
	 * If a template was loaded the template object's save() method has to be
	 * used instead of a writer
	 *
	 * @param	string	$savePath	 The path to which the output will be written
	 * @param   string $type         The type of the writer
	 * @return	void
	 */
	public function save($savePath = '', $type = NULL) {
		if (!$savePath) {
			$savePath = $this->getSavePath();
		}
		if (is_a($this->getDriver(), 'PHPWord_Template')) {
			$this->_callMethodIfExists('willSaveDocument');
			$this->getDriver()->save($savePath);
			return;
		}
		parent::save($savePath, $type);
	}

	/**
	 * Outputs the rendered data directly to the browser.
	 *
	 * @param	string $name	 This appears as the name of the downloaded file
	 * @param   string $type     The type of the writer
	 * @return	void
	 */
	public function output($name = '', $type = NULL) {
		if (!$name) {
			$name = basename($this->getSavePath());
		}
		if (is_a($this->getDriver(), 'PHPWord_Template')) {
			$this->sendHeaders($name, $type);

			$this->_callMethodIfExists('willSaveDocument');
			$this->getDriver()->save('php://output');
			return;
		}
		parent::save($savePath, $type);
	}
}
?>