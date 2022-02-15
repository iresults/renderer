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
 * The interface for all multi page PDF rendering classes.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Renderer_Pdf
 */
interface MultiPageInterface
{
    /**
     * Returns the current page number.
     *
     * @return    int
     */
    public function getPageNumber();

    /**
     * Adds a new page with the given configuration.
     *
     * @param    string $orientation P for portrait or L for landscape
     * @param    mixed  $format      The format used for pages. It can be either: a
     *                               string values describing a page format or an array of parameters.
     *
     * @return    void
     */
    public function addPage($orientation = '', $format = '');

    /**
     * Prevents the engine from adding a page break.
     *
     * @return    void
     */
    public function lockPageBreak();

    /**
     * Attempts to acquire a page break lock and immediately returns if the
     * attempt was successful.
     *
     * @return    boolean    Returns if the lock could be acquired
     */
    public function tryLockPageBreak();

    /**
     * Unlocks the page break lock.
     *
     * @return    void
     */
    public function unlockPageBreak();
}