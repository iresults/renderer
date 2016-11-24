<?php
/**
 * Created by PhpStorm.
 * User: cod
 * Date: 24.11.16
 * Time: 09:58
 */

namespace Iresults\Renderer\Tests\Unit\Pdf\Engine\Html;


use Iresults\Renderer\Pdf\Engine\Html\HtmlInterface;
use Iresults\Renderer\Pdf\Engine\Html\MpdfHtml;

class MpdfHtmlTest extends AbstractHtmlEngineCase
{
    public function buildEngine()
    {
        if (class_exists('mPDF')) {
            return new MpdfHtml();
        }

        $this->markTestSkipped('Requires mPDF to run');

        return null;
    }


}
