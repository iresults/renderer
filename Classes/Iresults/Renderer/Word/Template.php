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
     * Messages for ZIP constants
     * @var array
     */
    static protected $zipConstants = array(
        \ZIPARCHIVE::CREATE              => 'Create the archive if it does not exist.',
        \ZIPARCHIVE::OVERWRITE           => 'Always start a new archive, this mode will overwrite the file if it already exists.',
        \ZIPARCHIVE::EXCL                => 'Error if archive already exists.',
        \ZIPARCHIVE::CHECKCONS           => 'Perform additional consistency checks on the archive, and error if they fail.',
        \ZIPARCHIVE::FL_NOCASE           => 'Ignore case on name lookup',
        \ZIPARCHIVE::FL_NODIR            => 'Ignore directory component',
        \ZIPARCHIVE::FL_COMPRESSED       => 'Read compressed data',
        \ZIPARCHIVE::FL_UNCHANGED        => 'Use original data, ignoring changes.',
        \ZIPARCHIVE::CM_DEFAULT          => 'better of deflate or store.',
        \ZIPARCHIVE::CM_STORE            => 'stored (uncompressed).',
        \ZIPARCHIVE::CM_SHRINK           => 'shrunk',
        \ZIPARCHIVE::CM_REDUCE_1         => 'reduced with factor 1',
        \ZIPARCHIVE::CM_REDUCE_2         => 'reduced with factor 2',
        \ZIPARCHIVE::CM_REDUCE_3         => 'reduced with factor 3',
        \ZIPARCHIVE::CM_REDUCE_4         => 'reduced with factor 4',
        \ZIPARCHIVE::CM_IMPLODE          => 'imploded',
        \ZIPARCHIVE::CM_DEFLATE          => 'deflated',
        \ZIPARCHIVE::CM_DEFLATE64        => 'deflate64',
        \ZIPARCHIVE::CM_PKWARE_IMPLODE   => 'PKWARE imploding',
        #\ZIPARCHIVE::CM_BZIP2            => 'BZIP2 algorithm',
        \ZIPARCHIVE::ER_OK               => 'No error.',
        \ZIPARCHIVE::ER_MULTIDISK        => 'Multi-disk zip archives not supported.',
        \ZIPARCHIVE::ER_RENAME           => 'Renaming temporary file failed.',
        \ZIPARCHIVE::ER_CLOSE            => 'Closing zip archive failed',
        \ZIPARCHIVE::ER_SEEK             => 'Seek error',
        \ZIPARCHIVE::ER_READ             => 'Read error',
        \ZIPARCHIVE::ER_WRITE            => 'Write error',
        \ZIPARCHIVE::ER_CRC              => 'CRC error',
        \ZIPARCHIVE::ER_ZIPCLOSED        => 'Containing zip archive was closed',
        \ZIPARCHIVE::ER_NOENT            => 'No such file.',
        \ZIPARCHIVE::ER_EXISTS           => 'File already exists',
        \ZIPARCHIVE::ER_OPEN             => 'Can\'t open file',
        \ZIPARCHIVE::ER_TMPOPEN          => 'Failure to create temporary file.',
        \ZIPARCHIVE::ER_ZLIB             => 'Zlib error',
        \ZIPARCHIVE::ER_MEMORY           => 'Memory allocation failure',
        \ZIPARCHIVE::ER_CHANGED          => 'Entry has been changed',
        \ZIPARCHIVE::ER_COMPNOTSUPP      => 'Compression method not supported.',
        \ZIPARCHIVE::ER_EOF              => 'Premature EOF',
        \ZIPARCHIVE::ER_INVAL            => 'Invalid argument',
        \ZIPARCHIVE::ER_NOZIP            => 'Not a zip archive',
        \ZIPARCHIVE::ER_INTERNAL         => 'Internal error',
        \ZIPARCHIVE::ER_INCONS           => 'Zip archive inconsistent',
        \ZIPARCHIVE::ER_REMOVE           => 'Can\'t remove file',
        \ZIPARCHIVE::ER_DELETED          => 'Entry has been deleted'
    );

    /**
     * Create a new Template Object
     *
     * @param string $strFilename
     */
    public function __construct($strFilename) {
        if (defined('ZIPARCHIVE::CM_BZIP2') && !isset(static::$zipConstants[12])) {
            static::$zipConstants[12] = 'BZIP2 algorithm';
        }

        $this->tempFileName = \Iresults\Core\Iresults::getTempPath() . str_replace('.', '_', basename($strFilename)) . '_' . time() . '.docx';
        $this->zipArchive = new \ZipArchive();

        if (!copy($strFilename, $this->tempFileName)) {
            throw new \UnexpectedValueException('Could not copy file "' . $strFilename . '" to temporary path "' . $this->tempFileName . '"', 1361205476);
        }

        $successfullyOpened = $this->zipArchive->open($this->tempFileName);
        if ($successfullyOpened !== TRUE) {
            $message = 'Error opening file "' . $this->tempFileName . '": ' . self::$zipConstants[$successfullyOpened];
            throw new \UnexpectedValueException($message, 1361205476);
        }
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
     * Hard replace the given search string with the data from replace
     *
     * Normally you should use assign() instead
     *
     * @param mixed $search
     * @param mixed $replace
     * @param boolean $regularExpression If set to TRUE preg_replace() will be used instead of str_replace()
     */
    public function replaceString($search, $replace, $regularExpression = FALSE) {
        if ($regularExpression) {
            $this->documentXML = preg_replace($search, $replace, $this->documentXML);
        } else {
            $this->documentXML = str_replace($search, $replace, $this->documentXML);
        }
    }

    /**
     * Returns the templates document XML data
     * @return string
     */
    public function getDocumentXML() {
        return $this->documentXML;
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
        return \Iresults\Core\Helpers\ObjectHelper::getObjectForKeyPathOfObject($keyPath, $this->templateVariableContainer, TRUE);
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

        $destinationDirectory = dirname($strFilename);
        if (!is_writable($destinationDirectory)) {
            throw new \UnexpectedValueException('Destination file directory "' . $destinationDirectory . '" is not writeable', 1361203866);
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

        if (!rename($this->tempFileName, $strFilename)) {
            throw new \UnexpectedValueException('Could not move file "' . $this->tempFileName . '" to destination path "' . $strFilename . '"', 1361264101);
        }
        $this->documentXML = $documentXmlLocal;
    }

}

