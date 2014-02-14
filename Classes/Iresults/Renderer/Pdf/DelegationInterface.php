<?php
namespace Iresults\Renderer\Pdf;

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
 * The interface for all multi page PDF rendering classes.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Renderer_Pdf
 */
interface DelegationInterface {
	/**
	 * Tries to call a method on the delegate or, if the delegate doesn't respond,
	 * the method will be tried on $this.
	 *
	 * @param	string	$method The name of the method to invoke
	 * @param	array	$arguments	 Optional arguments to pass to the object
	 * @param	object	$object	 Optional object to be checked first
	 * @return	mixed
	 */
	public function callMethodIfExists($method, $arguments = array(), $object = NULL);

	/**
	 * Returns the delegate
	 *
	 * @return	object
	 */
	public function getDelegate();

	/**
	 * Set a new delegate
	 *
	 * @param	object 	$delegate
	 * @return		void
	 */
	public function setDelegate($delegate);

	/**
	 * The header function that will call the delegates pdfHeader-method.
	 *
	 * return void
	 */
	public function Header();

	/**
	 * The footer function that will call the delegates pdfFooter-method.
	 *
	 * return void
	 */
	public function Footer();

	/*
	 * Example implementation for callMethodIfExists().
	 * This implementation simply calls the protected _callMethodIfExists().
	 */
	/*
	public function callMethodIfExists($method, $arguments = array(), $object = NULL){
		return $this->_callMethodIfExists($method, $arguments, $object);
	}
	 */
}