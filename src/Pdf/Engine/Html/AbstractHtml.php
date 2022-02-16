<?php
declare(strict_types=1);

namespace Iresults\Renderer\Pdf\Engine\Html;

use Iresults\Renderer\Exception\InvalidPathException;

abstract class AbstractHtml implements HtmlInterface
{
    /**
     * Drawing context
     *
     * @var MpdfHtml|object
     */
    protected $context;

    /**
     * Path the file will be saved at
     *
     * @var string
     */
    protected $savePath = '';

    /**
     * Path to the template file
     *
     * @var string
     */
    protected $templatePath = '';

    /**
     * The HTML template
     *
     * @var string
     */
    protected $template = '';

    /**
     * Path to the styles to be added to the PDF
     *
     * @var string
     */
    protected $stylesPath = '';

    /**
     * Styles for the PDF
     *
     * @var string
     */
    protected $styles = '';

    /**
     * Defines if the template needs to be rendered
     *
     * @var bool
     */
    protected $_needsToRender = true;

    /**
     * Render the PDF
     */
    abstract protected function _render();

    public function initWithTemplate(string $templatePath): HtmlInterface
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    public function save(string $savePath = '', string $type = null): void
    {
        if (!$savePath) {
            $savePath = $this->getSavePath();
        }
        if (!$savePath) {
            throw new InvalidPathException('No save path given', 1467642145);
        }
        $this->render();
        $this->getContext()->Output($savePath, 'F');
    }

    public function output(string $name = '', string $type = null): void
    {
        if (!$name) {
            $name = basename($this->getSavePath());
        }

        $this->sendHeaders($name, $type);

        $this->render();
        $this->getContext()->Output($name, 'D');
    }

    public function outputAndExit(string $name = '', string $type = null): void
    {
        $this->output($name, $type);

        exit();
    }

    public function render(): void
    {
        if ($this->_needsToRender) {
            $this->_render();
            $this->_needsToRender = false;
        }
    }

    public function getSavePath(): string
    {
        return $this->savePath;
    }

    public function setSavePath(string $savePath): \Iresults\Renderer\RendererInterface
    {
        $this->savePath = $savePath;

        return $this;
    }

    public function setContext($context): object
    {
        $this->_needsToRender = true;
        $this->context = $context;

        return $this;
    }

    public function getTemplate(): string
    {
        if (!$this->template) {
            if ($this->templatePath) {
                return file_get_contents($this->templatePath);
            }
        }

        return $this->template;
    }

    public function setTemplate(string $template): HtmlInterface
    {
        $this->_needsToRender = true;
        $this->template = $template;

        return $this;
    }

    public function setTemplatePath(string $templatePath): HtmlInterface
    {
        $this->_needsToRender = true;

        if (!file_exists($templatePath)) {
            throw new InvalidPathException(
                sprintf('Template path "%s" could not be found', $templatePath),
                1429523757
            );
        }
        if (!is_readable($templatePath)) {
            throw new InvalidPathException(
                sprintf('Template path "%s" is not readable', $templatePath),
                1429523758
            );
        }

        $this->templatePath = $templatePath;

        return $this;
    }

    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    public function setStyles(string $styles): HtmlInterface
    {
        $this->_needsToRender = true;

        $this->styles = $styles;
        if (file_exists($styles)) {
            $styles = file_get_contents($styles);
        }
        $this->getContext()->WriteHTML($styles, 1);

        return $this;
    }

    public function getStyles(): string
    {
        if (!$this->styles) {
            if ($this->stylesPath) {
                return file_get_contents($this->stylesPath);
            }
        }

        return $this->styles;
    }

    public function setStylesPath(string $stylesPath): HtmlInterface
    {
        $this->_needsToRender = true;

        if (!file_exists($stylesPath)) {
            throw new InvalidPathException(
                sprintf('Styles path "%s" could not be found', $stylesPath),
                1429523747
            );
        }
        if (!is_readable($stylesPath)) {
            throw new InvalidPathException(
                sprintf('Styles path "%s" is not readable', $stylesPath),
                1429523748
            );
        }
        $this->stylesPath = $stylesPath;

        return $this;
    }

    public function getStylesPath(): string
    {
        return $this->stylesPath;
    }

    /**
     * Send the headers for direct output of the rendered data.
     *
     * @param string $name This appears as the name of the downloaded file
     * @param null   $type Type for which to send the header
     * @return void
     */
    public function sendHeaders(string $name, $type = null): void
    {
        // Will be sent by mPDF
    }
}
