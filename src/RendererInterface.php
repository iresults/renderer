<?php
declare(strict_types=1);

namespace Iresults\Renderer;

use Iresults\Renderer\Pdf\Wrapper\MpdfWrapper\MpdfWrapperInterface;

interface RendererInterface
{
    /**
     * Write the rendered data to the given path
     *
     * @param string      $savePath The path to which the output will be written
     * @param string|null $type     The type of the writer
     * @return void
     */
    public function save(string $savePath = '', string $type = null): void;

    /**
     * Output the rendered data directly to the browser
     *
     * @param string      $name This appears as the name of the downloaded file
     * @param string|null $type The type of the writer
     * @return void
     */
    public function output(string $name = '', string $type = null): void;

    /**
     * Output the rendered data directly to the browser and exit script execution
     *
     * @param string      $name This appears as the name of the downloaded file
     * @param string|null $type The type of the writer
     * @return void
     */
    public function outputAndExit(string $name = '', string $type = null): void;

    /**
     * Return the path the file will be saved at
     *
     * @return string
     */
    public function getSavePath(): string;

    /**
     * Set the path the file will be saved at
     *
     * @param string $savePath
     * @return RendererInterface
     */
    public function setSavePath(string $savePath): RendererInterface;

    /**
     * Return the current rendering context (i.e. a mPDF wrapper)
     *
     * @return object|MpdfWrapperInterface
     */
    public function getContext(): object;

    /**
     * Set the current rendering context (i.e. a mPDF wrapper)
     *
     * @param object|MpdfWrapperInterface $context
     */
    public function setContext($context): object;
}
