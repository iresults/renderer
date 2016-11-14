<?php
namespace Iresults\Renderer;

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
use Iresults\Renderer\Pdf\Wrapper\MpdfWrapper;

/**
 * @author COD
 *         Created 07.10.13 17:54
 */
interface RendererInterface
{
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* COMMON RENDERER METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Writes the rendered data to the given path
     *
     * @param string $savePath The path to which the output will be written
     * @param string $type     The type of the writer
     * @return void
     */
    public function save($savePath = '', $type = null);

    /**
     * Outputs the rendered data directly to the browser
     *
     * @param string $name This appears as the name of the downloaded file
     * @param string $type The type of the writer
     * @return void
     */
    public function output($name = '', $type = null);

    /**
     * Outputs the rendered data directly to the browser and exit script execution
     *
     * @param string $name This appears as the name of the downloaded file
     * @param string $type The type of the writer
     * @return void
     */
    public function outputAndExit($name = '', $type = null);

    /**
     * Returns the path the file will be saved at
     *
     * @return string
     */
    public function getSavePath();

    /**
     * Sets the path the file will be saved at
     *
     * @param String $savePath
     */
    public function setSavePath($savePath);

    /**
     * Returns the current rendering context (i.e. a section or page)
     *
     * @return object|MpdfWrapper|\PHPWord_Section
     */
    public function getContext();

    /**
     * Set the current rendering context (i.e. a section or page)
     *
     * @param object|MpdfWrapper|\PHPWord_Section $context
     */
    public function setContext($context);
}