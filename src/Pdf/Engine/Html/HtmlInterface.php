<?php
declare(strict_types=1);

namespace Iresults\Renderer\Pdf\Engine\Html;

use Iresults\Renderer\Exception\InvalidPathException;
use Iresults\Renderer\RendererInterface;

/**
 * Interface for the HTML PDF engine
 */
interface HtmlInterface extends RendererInterface
{
    /**
     * Initialize with the given template file path
     *
     * @param string $templatePath
     * @return HtmlInterface
     */
    public function initWithTemplate(string $templatePath): HtmlInterface;

    /**
     * Return the HTML template to be rendered
     *
     * @return string
     */
    public function getTemplate(): string;

    /**
     * Set the HTML template to be rendered
     *
     * @param string $template
     * @return HtmlInterface
     */
    public function setTemplate(string $template): HtmlInterface;

    /**
     * Set the path to the HTML template to be rendered
     *
     * Note: The template path has a higher priority than the template property
     *
     * @param string $templatePath
     * @return HtmlInterface
     * @throws InvalidPathException if the given path does not exist or is not readable
     */
    public function setTemplatePath(string $templatePath): HtmlInterface;

    /**
     * Return the path to the HTML template to be rendered
     *
     * Note: The template path has a higher priority than the template property
     *
     * @return string
     */
    public function getTemplatePath(): string;

    /**
     * Add the given styles to the PDF
     *
     * @param string $styles Either a file path or the styles as string
     * @return HtmlInterface
     */
    public function setStyles(string $styles): HtmlInterface;

    /**
     * Return the HTML template to be rendered
     *
     * @return string
     */
    public function getStyles(): string;

    /**
     * Set the path to the styles to be added to the PDF
     *
     * Note: The style path has a higher priority than the styles property
     *
     * @param string $stylesPath
     * @return HtmlInterface
     * @throws InvalidPathException if the given path does not exist or is not readable
     */
    public function setStylesPath(string $stylesPath): HtmlInterface;

    /**
     * Return the path to the styles to be added to the PDF
     *
     * Note: The style path has a higher priority than the styles property
     *
     * @return string
     */
    public function getStylesPath(): string;

    /**
     * Render the template
     *
     * @return void
     */
    public function render(): void;
}
