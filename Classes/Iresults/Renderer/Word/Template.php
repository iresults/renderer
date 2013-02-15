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
 * An enhanced version of the PHPWord_Template
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults\Word
 */
class Template extends \PHPWord_Template {
    /**
     * Pattern to match varialbe expressions
     */
    const EXPRESSION_PATTERN = '!{[a-zA-Z0-9.]*}!i';

    /**
     * ZipArchive
     *
     * @var ZipArchive
     */
    protected $zipArchive;

    /**
     * Temporary Filename
     *
     * @var string
     */
    protected $tempFileName;

    /**
     * Document XML
     *
     * @var string
     */
    protected $documentXML;

    /**
     * Container for variables associated by the registered key
     * @var array
     */
    protected $templateVariableContainer = array();

    /**
     * Create a new Template Object
     *
     * @param string $strFilename
     */
    public function __construct($strFilename) {
        $path = dirname($strFilename);
        $this->tempFileName = $path . DIRECTORY_SEPARATOR . time() . '.docx';

        copy($strFilename, $this->tempFileName); // Copy the source File to the temp File


        $this->zipArchive = new \ZipArchive();
        $this->zipArchive->open($this->tempFileName);

        $this->documentXML = $this->zipArchive->getFromName('word/document.xml');
    }

    /**
     * Assign a value to the variable container.
     *
     * @param string $key The key of a view variable to set
     * @param mixed $value The value of the view variable
     * @return Tx_Fluid_View_AbstractTemplateView the instance of this view to allow chaining
     * @api
     */
    public function assign($key, $value) {
        $this->templateVariableContainer[$key] = $value;
        return $this;
    }

    /**
     * Assigns multiple values to the JSON output.
     * However, only the key "value" is accepted.
     *
     * @param array $values Keys and values - only a value with key "value" is considered
     * @return Tx_Fluid_View_AbstractTemplateView the instance of this view to allow chaining
     * @api
     */
    public function assignMultiple(array $values) {
        foreach ($values as $key => $value) {
            $this->templateVariableContainer[$key] = $value;
        }
        return $this;
    }

    /**
     * Returns the template variable container
     * @return Tx_Fluid_Core_ViewHelper_TemplateVariableContainer
     */
    public function getTemplateVariableContainer() {
        return $this->templateVariableContainer;
    }

    /**
     * Set a Template value
     *
     * @param mixed $search
     * @param mixed $replace
     */
    public function setValue ($search, $replace) {
        return $this->assign($search, $replace);
    }

    /**
     * Tries to fetch the value for the given key path
     *
     * The entry point is the first part of the key path, which is the array key
     * inside of $templateVariableContainer
     *
     * @param  string $keyPath Object key path in the format "templateVariableName.property"
     * @return mixed
     */
    protected function getValueForExpression($keyPath) {
        if ($keyPath[0] === '{') {
            $keyPath = substr($keyPath, 1, -1);
        }
        return \Tx_Iresults_Helpers_ObjectHelper::getObjectForKeyPathOfObject($keyPath, $this->templateVariableContainer, TRUE);
    }

    /**
     * Save Template
     *
     * @param string $strFilename
     */
    public function save($strFilename) {
        // Search for expressions inside the XML document
        $documentXmlLocal = $this->documentXML;
        if (preg_match_all(self::EXPRESSION_PATTERN, $documentXmlLocal, $variableExpressions)) {
            $variableExpressions = reset($variableExpressions);
            foreach ($variableExpressions as $keyPath) {
                $value = $this->getValueForExpression($keyPath);
                $documentXmlLocal = str_replace($keyPath, $value, $documentXmlLocal);
            }
        }


        // Remove the destination, if it already exists
        if (file_exists($strFilename)) {
            unlink($strFilename);
        }

        $this->zipArchive->addFromString('word/document.xml', $documentXmlLocal);

        // Close zip file
        if ($this->zipArchive->close() === FALSE) {
            throw new \Exception('Could not close zip file.', 1360944963);
        }

        rename($this->tempFileName, $strFilename);
        $this->documentXML = $documentXmlLocal;
    }

}

