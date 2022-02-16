<?php
declare(strict_types=1);

namespace Iresults\Renderer\Tests\Unit\Pdf\Engine\Html;

use Iresults\Renderer\Pdf\Engine\Html\MpdfHtml;
use Iresults\Renderer\RendererInterface;
use function class_exists;

class MpdfHtmlTest extends AbstractHtmlEngineCase
{
    use HtmlEngineTestSuite;

    public function buildEngine(): RendererInterface
    {
        if (class_exists('mPDF') || class_exists(\Mpdf\Mpdf::class)) {
            return new MpdfHtml();
        }

        $this->markTestSkipped('Requires mPDF to run');
    }
}
