<?php
namespace Iresults\Renderer;

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

use Iresults\Core\Model;

/**
 * The abstract class for the renderers
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults\Word
 */
abstract class AbstractRenderer extends Model {
	/**
	 * @var string The path the file will be saved.
	 */
	protected $savePath = '';

	/**
	 * The underlaying object responsible for rendering
	 * @var PHPWord
	 */
	protected $driver = NULL;

	/**
	 * The current context of the rendering (i.e. a section)
	 * @var PHPWord_Section
	 */
	protected $context = NULL;

	/**
	 * The default writer type
	 * @var string
	 */
	protected $defaultWriterType = '';

	/**
	 * The constructor
	 *
	 * @param	array   $parameters
	 * @return	Iresults\Word\AbstractRenderer
	 */
	public function __construct(array $parameters = array()) {
		parent::__construct($parameters);
		if (isset($parameters['delegate'])) {
			$this->_delegate = $parameters['delegate'];
		}
		$this->initializeDriver();
		return $this;
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* FACTORY METHODS           MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Create a new instance with the given template file path
	 * @param  string $templateFilePath
	 * @return AbstractRenderer
	 */
	static public function rendererWithTemplate($templateFilePath) {
		$renderer = new static();
		return $renderer->initWithTemplate($templateFilePath);
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* COMMON RENDERER METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Writes the rendered data to the given path.
	 *
	 * @param	string	$savePath	 The path to which the output will be written
	 * @param   string $type         The type of the writer
	 * @return	void
	 */
	public function save($savePath = '', $type = NULL) {
		if (!$savePath) {
			$savePath = $this->getSavePath();
		}
		$writer = $this->createWriter($type);
		$this->_callMethodIfExists('willSaveDocument', array($writer));
		$writer->save($savePath);
	}

	/**
	 * Outputs the rendered data directly to the browser.
	 *
	 * @param	string	$name	 This appears as the name of the downloaded file
	 * @param   string $type         The type of the writer
	 * @return	void
	 */
	public function output($name = '', $type = NULL) {
		if (!$name) {
			$name = basename($this->getSavePath());
		}
		$this->sendHeaders($name, $type);

		$writer = $this->createWriter($type);
		$this->_callMethodIfExists('willSaveDocument', array($writer));
		$writer->save('php://output');
	}

	/**
	 * Returns the driver instance
	 * @return PHPWord
	 */
	public function getDriver() {
		return $this->driver;
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
	 */
	public function setSavePath($savePath) {
	    $this->savePath = $savePath;
	    return $this;
	}

	/**
	 * Returns the current rendering context
	 *
	 * @return PHPWord_Section
	 */
	public function getContext() {
	    return $this->context;
	}

	/**
	 * Set the current rendering context
	 *
	 * @param PHPWord_Section $context
	 */
	public function setContext($context) {
	    $this->context = $context;
	    return $this;
	}

	/**
	 * Tries to forward undefined methods to the driver
	 *
	 * @param	string	$name		The originally called method
	 * @param	array	$arguments	The arguments passed to the original method
	 * @return	mixed
	 * @throws	BadMethodCallException	If no dynamic method was found
	 */
	public function __call($name, array $arguments) {
		if (method_exists($this->getDriver(), $name)) {
			return call_user_func_array(array($this->getDriver(), $name), $arguments);
		}
		if (method_exists($this->getContext(), $name)) {
			return call_user_func_array(array($this->getContext(), $name), $arguments);
		}
		return parent::__call($name, $arguments);
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* DRIVER SPECIFIC METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Creates and prepares the driver instance
	 * @return PHPWord Returns the driver
	 */
	abstract public function initializeDriver();

	/**
	 * Returns a new writer instance
	 * @param	string $type The type of the writer
	 * @return	PHPWord_Writer_IWriter
	 */
	abstract public function createWriter($type = NULL);

	/**
	 * Sends the headers for direct output of the rendered data.
	 *
	 * @param	string $name	This appears as the name of the downloaded file
	 * @param   string $type	Type for which to send the header
	 * @return	boolean Returns TRUE if the headers are successfully sent, else FALSE
	 */
	abstract public function sendHeaders($name, $type = NULL);

	/**
	 * Initialize a new instance with the given template file path
	 * @param  string $templateFilePath
	 * @return AbstractRenderer
	 */
	abstract public function initWithTemplate($templateFilePath);
}
?>