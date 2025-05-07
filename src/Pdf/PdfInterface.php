<?php

declare(strict_types=1);

namespace Iresults\Renderer\Pdf;

use Iresults\Renderer\RendererInterface;

/**
 * Interface for PDF rendering classes
 */
interface PdfInterface extends DelegationInterface, MultiPageInterface, RendererInterface
{
}
