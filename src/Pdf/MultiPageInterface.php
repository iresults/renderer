<?php
declare(strict_types=1);

namespace Iresults\Renderer\Pdf;

/**
 * Interface for all multi-page PDF rendering classes
 */
interface MultiPageInterface
{
    /**
     * Return the current page number
     *
     * @return int
     */
    public function getPageNumber(): int;

    /**
     * Add a new page with the given configuration
     *
     * @param string       $orientation 'P' for portrait or 'L' for landscape
     * @param string|array $format      The format used for pages. It can be either: a
     *                                  string values describing a page format or an array of parameters.
     *
     * @return void
     */
    public function addPage(string $orientation = '', $format = '');

    /**
     * Prevent the engine from adding a page break
     *
     * @return void
     */
    public function lockPageBreak();

    /**
     * Attempt to acquire a page break lock and immediately return if the attempt was successful
     *
     * @return boolean Returns if the lock could be acquired
     */
    public function tryLockPageBreak(): bool;

    /**
     * Unlock the page break lock
     *
     * @return void
     */
    public function unlockPageBreak();
}
