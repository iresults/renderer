<?php
declare(strict_types=1);

namespace Iresults\Renderer\Word;

use Iresults\Core\Iresults;
use Iresults\Renderer\AbstractRenderer as AbstractRenderer;
use Iresults\Renderer\RendererInterface;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use function sprintf;

/**
 * The Word renderer
 */
class Renderer extends AbstractRenderer
{
    /**
     * The default writer type
     *
     * @var string
     */
    protected $defaultWriterType = 'Word2007';

    public function initializeDriver(): object
    {
        $this->driver = new PhpWord();
        $this->context = $this->driver->addSection();

        return $this->driver;
    }

    /**
     * Return a new writer instance
     *
     * @param string|null $type The type of the writer
     * @return object
     */
    public function createWriter(string $type = null): object
    {
        if (!$type) {
            $type = $this->defaultWriterType;
        }

        return IOFactory::createWriter($this->getDriver(), $type);
    }

    ///**
    // * Sends the headers for direct output of the rendered data.
    // *
    // * @param string      $name This appears as the name of the downloaded file
    // * @param string|null $type Type for which to send the header
    // * @return boolean Returns TRUE if the headers are successfully sent, else FALSE
    // */
    //public function sendHeaders(string $name, string $type = null)
    //{
    //    if (!$type) {
    //        $type = $this->defaultWriterType;
    //    }
    //}

    /**
     * Initialize a new instance with the given template file path
     *
     * @param string $templateFilePath
     * @return RendererInterface
     */
    public function initWithTemplate(string $templateFilePath): RendererInterface
    {
        $templateFilePath = Iresults::getPathOfResource($templateFilePath);
        if (!is_readable($templateFilePath)) {
            throw new \UnexpectedValueException(
                sprintf('Template file "%s" is not readable', $templateFilePath),
                1360939616
            );
        }
        $templateDriver = new Template($templateFilePath);
        $this->driver = $templateDriver;
        $this->context = $templateDriver;

        return $this;
    }

    public function save(string $savePath = '', string $type = null): void
    {
        if (!$savePath) {
            $savePath = $this->getSavePath();
        }
        if ($this->getDriver() instanceof TemplateProcessor) {
            $this->_callMethodIfExists('willSaveDocument');
            $this->getDriver()->save($savePath);

            return;
        }
        parent::save($savePath, $type);
    }

    public function output(string $name = '', string $type = null): void
    {
        if (!$name) {
            $name = basename($this->getSavePath());
        }
        if ($this->getDriver() instanceof TemplateProcessor) {
            $this->sendHeaders($name, $type);

            $this->_callMethodIfExists('willSaveDocument');
            $this->getDriver()->save('php://output');

            return;
        }
        parent::save($name, $type);
    }
}
