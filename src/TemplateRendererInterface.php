<?php
declare(strict_types=1);

namespace Iresults\Renderer;

interface TemplateRendererInterface extends RendererInterface
{
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* FACTORY METHODS           MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Create a new instance with the given template file path
     *
     * @param string $templateFilePath
     * @return RendererInterface
     */
    public static function rendererWithTemplate(string $templateFilePath): RendererInterface;
}
