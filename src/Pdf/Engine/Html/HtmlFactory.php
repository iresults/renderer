<?php
declare(strict_types=1);

namespace Iresults\Renderer\Pdf\Engine\Html;

use Iresults\Renderer\Helpers\AbstractFactory;

/**
 * Factory for HTML PDF engines
 */
class HtmlFactory extends AbstractFactory
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
        $instance = static::createInstance($constructorArguments);

        return $instance;
    }

    protected static function getFactoryClass(): ?string
    {
        if (class_exists('mPDF')) {
            return MpdfHtml::class;
        }

        return null;
    }

    /**
     * Return a new canvas renderer with the given template
     *
     * @param string $template
     * @return HtmlInterface
     */
    static public function rendererWithTemplate(string $template): HtmlInterface
    {
        $instance = static::renderer();

        return $instance->initWithTemplate($template);
    }
}
