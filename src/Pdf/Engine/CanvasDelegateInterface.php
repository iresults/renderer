<?php

declare(strict_types=1);

namespace Iresults\Renderer\Pdf\Engine;

/**
 * Interface describing the methods called on the delegate of a canvas engine
 */
interface CanvasDelegateInterface
{
    /**
     * Invoked when no template was found in the original class
     *
     * May return a template script content to render.
     *
     * @return string|null The template script to render
     */
    public function getTemplate(): ?string;

    /**
     * Invoked before the scripts are drawn
     */
    public function willDraw(): void;

    /**
     * Invoked at the end of the draw script
     */
    public function didDraw(): void;

    /**
     * Invoked before the template script is loaded
     */
    public function willRender(): void;

    /**
     * Invoked when the PDF did render
     */
    public function didRender(): void;

    /**
     * Invoked when the header should be rendered and no header script is found in the template
     */
    public function header(): void;

    /**
     * Invoked when the footer should be rendered and no footer script is found in the template
     */
    public function footer(): void;
}
