<?php
/**
 * Created by PhpStorm.
 * User: cod
 * Date: 24.11.16
 * Time: 09:58
 */

namespace Iresults\Renderer\Tests\Unit\Pdf\Engine\Html;


use Iresults\Renderer\Pdf\Engine\Html\HtmlInterface;
use Iresults\Renderer\Tests\Unit\Pdf\Engine\AbstractEngineCase;

abstract class AbstractHtmlEngineCase extends AbstractEngineCase
{
    /**
     * Builds a temporary PDF and checks if the texts have the correct number of occurrences
     *
     * @param array         $textsAndCount   Texts and expected number of occurrences
     * @param callable|null $configureEngine Callback to configure the PDF
     */
    protected function pdfWithTextsCount(array $textsAndCount, callable $configureEngine = null)
    {
        $engine = $this->buildEngine();
        $pdfPath = $this->getTempPath();
        $engine->setSavePath($pdfPath);

        $configureEngine($engine, $pdfPath);

        $engine->save();
        $this->assertPdfContainsTextsCount($pdfPath, $textsAndCount);
    }

    /**
     * Builds two temporary PDFs: one from a template file and one from body string
     *
     * @param array         $texts           Texts and expected number of occurrences
     * @param string        $body            HTML body to write to the PDF
     * @param callable|null $configureEngine Callback to configure the PDF
     */
    protected function pdfWithTextsCountAndBody(array $texts, $body, callable $configureEngine = null)
    {
        $templatePath = $this->getTempPath();
        file_put_contents($templatePath, $body);

        $configureEngine = $configureEngine ?: function () {
        };

        $this->pdfWithTextsCount(
            $texts,
            function (HtmlInterface $engine, $pdfPath) use ($configureEngine, $body) {
                $engine->setTemplate($body);
                $configureEngine($engine, $pdfPath);
            }
        );
        $this->pdfWithTextsCount(
            $texts,
            function (HtmlInterface $engine, $pdfPath) use ($configureEngine, $templatePath) {
                $engine->setTemplatePath($templatePath);
                $configureEngine($engine, $pdfPath);
            }
        );
    }

    /**
     * @test
     */
    public function generatePdfFromBodyTest()
    {
        $text = 'This is the testing text that should be written in the PDF';

        $this->pdfWithTextsCount(
            [$text => -1],
            function (HtmlInterface $engine) use ($text) {
                $engine->setTemplate($this->getBodySinglePage($text));
            }
        );
    }

    /**
     * @test
     */
    public function generatePdfFromTemplateTest()
    {
        $text = 'This is the testing text that should be written in the PDF';

        $templatePath = $this->getTempPath();
        file_put_contents($templatePath, $this->getBodySinglePage($text));

        $this->pdfWithTextsCount(
            [$text => -1],
            function (HtmlInterface $engine) use ($templatePath) {
                $engine->setTemplatePath($templatePath);
            }
        );
    }

    /**
     * @test
     */
    public function generatePdfFromTemplateMultiPageTest()
    {
        $page1 = 'This is the testing text that should be written in the PDF on page 1';
        $page2 = 'This should be written on page 2';

        $this->pdfWithTextsCountAndBody(
            [$page1 => 1, $page2 => 1,],
            $this->getBodyMultiPage($page1, $page2)
        );
    }

    /**
     * @test
     */
    public function generatePdfFromBodyWithHeaderMultiPageTest()
    {
        $header = 'This is the header';

        $this->pdfWithTextsCountAndBody(
            [$header => 2],
            $this->getBodyMultiPage(
                'This is the testing text that should be written in the PDF on page 1',
                'This should be written on page 2'
            ),
            function (HtmlInterface $engine) use ($header) {
                $engine->getContext()->SetHTMLHeader("<header>$header</header>");
            }
        );
    }

    /**
     * @test
     */
    public function generatePdfFromBodyWithFooterMultiPageTest()
    {
        $footer = 'This is the footer';

        $this->pdfWithTextsCountAndBody(
            [$footer => 2],
            $this->getBodyMultiPage(
                'This is the testing text that should be written in the PDF on page 1',
                'This should be written on page 2'
            ),
            function (HtmlInterface $engine) use ($footer) {
                $engine->getContext()->SetFooter("<footer>$footer</footer>");
            }
        );
    }

    /**
     * @param $text
     * @return string
     */
    protected function getBodySinglePage($text)
    {
        return "<html><body><section><p style='color: blue'>$text</p></section></body></html>";
    }

    /**
     * @param string[] ...$pages
     * @return string
     */
    protected function getBodyMultiPage(... $pages)
    {
        $sections = array();
        foreach ($pages as $page) {
            $sections[] = "<section><p style='color: blue'>$page</p></section>";
        }

        return sprintf('<html><body>%s</body></html>', implode('<pagebreak />' . PHP_EOL, $sections));
    }
}
