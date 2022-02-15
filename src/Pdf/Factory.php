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


/**
 * The iresults addition to the FPDF library.
 * It includes a delegate to output the header and footer. The delegate methods
 * pdfHeader() and pdfFooter() are invoked automatically if a new page is insert.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Pdf
 */
class Factory extends \Iresults\Core\Core
{
    /**
     * Returns the best available PDF object.
     *
     * @param    array $parameters Parameters to pass to the constructor
     * @return    PdfInterface
     */
    static public function makeInstance($parameters = null)
    {
        $instance = null;

        if (class_exists('TCPDF')) {
            $className = 'Tx_Iresults_Renderer_Pdf_Tcpdf_Tcpdf';
        } elseif (class_exists('FPDF', true)) {
            $className = 'Tx_Iresults_Renderer_Pdf_Fpdf_Fpdf';
        } else {
            return null;
        }

        return self::createInstanceOfClassNameWithArguments($className, $parameters);
    }

    /**
     * Creates an instance of the given class with the given arguments.
     *
     * @param    string $className  The class to create an instance of
     * @param    array  $parameters The parameters to pass to the constructor
     * @return    PdfInterface
     */
    static protected function createInstanceOfClassNameWithArguments($className, $parameters)
    {
        if ($parameters === null) {
            return new $className();
        }
        /*
         * Apply the parameters to the constructor.
         * @see http://stackoverflow.com/questions/2409237/how-to-call-the-constructor-with-call-user-func-array-in-php
         */
        $reflect = new \ReflectionClass($className);
        $instance = $reflect->newInstanceArgs($parameters);

        return $instance;
    }
}