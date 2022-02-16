<?php
declare(strict_types=1);

namespace Iresults\Renderer\Pdf\Engine\Html;

use Iresults\Renderer\Helpers\ObjectBuilder;
use UnexpectedValueException;
use function class_exists;
use function get_called_class;

/**
 * Factory for HTML PDF engines
 */
class HtmlFactory
{
    /**
     * Return a new HTML renderer
     *
     * @param array $constructorArguments Optional arguments to pass to the constructor
     * @return HtmlInterface
     */
    public static function renderer(array $constructorArguments = []): HtmlInterface
    {
        /** @var HtmlInterface $instance */
        $instance = ObjectBuilder::createInstance(static::getFactoryClass(), $constructorArguments);

        return $instance;
    }

    /**
     * Return a new HTML renderer with the given template
     *
     * @param string $template
     * @return HtmlInterface
     */
    public static function rendererWithTemplate(string $template): HtmlInterface
    {
        $instance = static::renderer();

        return $instance->initWithTemplate($template);
    }

    private static function getFactoryClass(): ?string
    {
        if (class_exists('mPDF') || class_exists(\Mpdf\Mpdf::class)) {
            return MpdfHtml::class;
        }

        throw new UnexpectedValueException('No implementation found in ' . get_called_class(), 1381327896);
    }
}
