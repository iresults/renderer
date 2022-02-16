<?php
declare(strict_types=1);

namespace Iresults\Renderer;

use Iresults\Core\Model;
use Iresults\Renderer\Pdf\Wrapper\MpdfWrapper\MpdfWrapperInterface;

/**
 * Abstract base class for renderers
 */
abstract class AbstractRenderer extends Model implements RendererInterface
{
    /**
     * @var string The path the file will be saved.
     */
    protected $savePath = '';

    /**
     * The underlying object responsible for rendering
     *
     * @var object|MpdfWrapperInterface
     */
    protected $driver = null;

    /**
     * The current context of the rendering (i.e. a mPDF wrapper)
     *
     * @var object|MpdfWrapperInterface
     */
    protected $context = null;

    /**
     * The constructor
     *
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);
        if (isset($parameters['delegate'])) {
            $this->_delegate = $parameters['delegate'];
        }
        $this->initializeDriver();
    }

    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* FACTORY METHODS           MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Create a new instance with the given template file path
     *
     * @param string $templateFilePath
     * @return RendererInterface
     */
    public static function rendererWithTemplate(string $templateFilePath): RendererInterface
    {
        $renderer = new static();

        return $renderer->initWithTemplate($templateFilePath);
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* COMMON RENDERER METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    public function save(string $savePath = '', string $type = null): void
    {
        if (!$savePath) {
            $savePath = $this->getSavePath();
        }
        $writer = $this->createWriter($type);
        $this->_callMethodIfExists('willSaveDocument', [$writer]);
        $writer->save($savePath);
    }

    public function output(string $name = '', string $type = null): void
    {
        if (!$name) {
            $name = basename($this->getSavePath());
        }
        $this->sendHeaders($name, $type);

        $writer = $this->createWriter($type);
        $this->_callMethodIfExists('willSaveDocument', [$writer]);
        $writer->save('php://output');
    }

    public function outputAndExit(string $name = '', string $type = null): void
    {
        $this->output($name, $type);

        exit();
    }

    /**
     * Return the driver instance
     *
     * @return object|MpdfWrapperInterface
     */
    public function getDriver()
    {
        return $this->driver;
    }

    public function getSavePath(): string
    {
        return $this->savePath;
    }

    public function setSavePath(string $savePath): RendererInterface
    {
        $this->savePath = $savePath;

        return $this;
    }

    public function getContext(): object
    {
        return $this->context;
    }

    public function setContext($context): object
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Try to forward undefined methods to the driver
     *
     * @param string $name      The originally called method
     * @param array  $arguments The arguments passed to the original method
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        if (method_exists($this->getDriver(), $name)) {
            return call_user_func_array([$this->getDriver(), $name], $arguments);
        }
        if (method_exists($this->getContext(), $name)) {
            return call_user_func_array([$this->getContext(), $name], $arguments);
        }

        return parent::__call($name, $arguments);
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* DRIVER SPECIFIC METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Create and prepare the driver instance
     *
     * @return object Returns the driver
     */
    abstract public function initializeDriver(): object;

    /**
     * Return a new writer instance
     *
     * @param string|null $type The type of the writer
     * @return object
     */
    abstract public function createWriter(string $type = null): object;

    /**
     * Initialize a new instance with the given template file path
     *
     * @param string $templateFilePath
     * @return RendererInterface
     */
    abstract public function initWithTemplate(string $templateFilePath): RendererInterface;

    /**
     * Send the headers for direct output of the rendered data
     *
     * @param string      $name This appears as the name of the downloaded file
     * @param string|null $type Type for which to send the header
     * @return void
     */
    public function sendHeaders(string $name, string $type = null): void
    {
    }
}
