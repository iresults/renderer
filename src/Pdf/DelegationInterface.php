<?php
declare(strict_types=1);

namespace Iresults\Renderer\Pdf;

/**
 * Interface for rendering classes with delegation support
 */
interface DelegationInterface
{
    /**
     * Try to call a method on the delegate or, if the delegate doesn't respond, the method will be tried on $this
     *
     * @param string      $method    The name of the method to invoke
     * @param array       $arguments Optional arguments to pass to the object
     * @param object|null $object    Optional object to be checked first
     * @return    mixed
     */
    public function callMethodIfExists(string $method, array $arguments = [], ?object $object = null);

    /**
     * Return the delegate
     *
     * @return    object
     */
    public function getDelegate(): object;

    /**
     * Set a new delegate
     *
     * @param object $delegate
     * @return void
     */
    public function setDelegate(object $delegate);

    /**
     * The header function that will call the delegate's pdfHeader-method
     *
     * return void
     */
    public function Header();

    /**
     * The footer function that will call the delegate's pdfFooter-method
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
