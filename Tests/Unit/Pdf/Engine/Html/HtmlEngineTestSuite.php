<?php
/**
 * Created by PhpStorm.
 * User: cod
 * Date: 24.11.16
 * Time: 13:34
 */

namespace Iresults\Renderer\Tests\Unit\Pdf\Engine\Html;


use Iresults\Renderer\Pdf\Engine\Html\HtmlInterface;
use Iresults\Renderer\Pdf\Wrapper\MpdfWrapper;
use Iresults\Renderer\Tests\Unit\Pdf\AssertionTrait;

trait HtmlEngineTestSuite
{
    use AssertionTrait;

    /**
     * @return HtmlInterface
     */
    abstract public function buildEngine();

    /**
     * @param string $suffix
     * @return string
     */
    abstract protected function getTempPath($suffix = 'pdf');

    /**
     * Builds a temporary PDF and checks if the texts have the correct number of occurrences
     *
     * @param array         $textsAndCount   Texts and expected number of occurrences
     * @param callable|null $configureEngine Callback to configure the PDF
     * @return string Returns the save path
     */
    protected function pdfWithTextsCount(array $textsAndCount, callable $configureEngine = null)
    {
        $engine = $this->buildEngine();
        $pdfPath = $this->getTempPath();
        $engine->setSavePath($pdfPath);

        $configureEngine($engine, $pdfPath);

        $engine->save();
        $this->assertPdfContainsTextsCount($pdfPath, $textsAndCount);

        return $pdfPath;
    }

    /**
     * Builds two temporary PDFs: one from a template file and one from body string
     *
     * @param array         $texts           Texts and expected number of occurrences
     * @param string        $body            HTML body to write to the PDF
     * @param callable|null $configureEngine Callback to configure the PDF
     * @return string[] Returns the save paths
     */
    protected function pdfWithTextsCountAndBody(array $texts, $body, callable $configureEngine = null)
    {
        $configureEngine = $configureEngine ?: function () {
        };

        // Generate the PDF from the given HTML body
        $pathFromHtmlBody = $this->pdfWithTextsCount(
            $texts,
            function (HtmlInterface $engine, $pdfPath) use ($configureEngine, $body) {
                $engine->setTemplate($body);
                $configureEngine($engine, $pdfPath);
            }
        );

        // Generate the PDF from the template file
        $templatePath = $this->getTempPath();
        file_put_contents($templatePath, $body);

        $pathFromTemplate = $this->pdfWithTextsCount(
            $texts,
            function (HtmlInterface $engine, $pdfPath) use ($configureEngine, $templatePath) {
                $engine->setTemplatePath($templatePath);
                $configureEngine($engine, $pdfPath);
            }
        );

        return [
            $pathFromHtmlBody,
            $pathFromTemplate,
            'pathFromHtmlBody' => $pathFromHtmlBody,
            'pathFromTemplate' => $pathFromTemplate,
        ];
    }

    /**
     * Builds a temporary PDF with the given body
     *
     * @param string        $body            HTML body to write to the PDF
     * @param boolean       $useTemplateFile Save the body into a file
     * @param callable|null $configureEngine Callback to configure the PDF
     * @return string Returns the save path
     */
    protected function pdfWithBody($body, $useTemplateFile, callable $configureEngine = null)
    {
        $engine = $this->buildEngine();
        $pdfPath = $this->getTempPath();
        $engine->setSavePath($pdfPath);

        // Generate the PDF from the template file
        if ($useTemplateFile) {
            $templatePath = $this->getTempPath();
            file_put_contents($templatePath, $body);
            $engine->setTemplatePath($templatePath);
        } else {
            $engine->setTemplate($body);
        }

        $configureEngine($engine, $pdfPath);

        $engine->save();

        return $pdfPath;
    }

    /**
     * @test
     */
    public function generateSinglePagePdfTest()
    {
        $text = 'This is the testing text that should be written in the PDF';

        $this->pdfWithTextsCountAndBody(
            [$text => -1],
            $this->getBodySinglePage($text)
        );
    }

    /**
     * @test
     */
    public function generateMultiPagePdfTest()
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
    public function generateMultiPagePdfWithHeaderTest()
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
    public function generateMultiPagePdfWithFooterTest()
    {
        $footer = 'This is the footer';

        $this->pdfWithTextsCountAndBody(
            [$footer => 2],
            $this->getBodyMultiPage(
                'This is the testing text that should be written in the PDF on page 1',
                'This should be written on page 2'
            ),
            function (HtmlInterface $engine) use ($footer) {
                $engine->getContext()->SetHTMLFooter("<footer>$footer</footer>");
            }
        );
    }

    /**
     * @test
     */
    public function generateMultiPagePdfWithInlineHeaderTest()
    {
        $header = 'This is the header';
        $this->pdfWithTextsCountAndBody([$header => 4], $this->getLongMultiPageBodyWithHeader($header));
    }

    /**
     * @test
     */
    public function generateMultiPagePdfWithInlineHeaderAndCustomContextTest()
    {
        $header = 'This is the header';

        $this->pdfWithTextsCountAndBody(
            [$header => 4],
            $this->getLongMultiPageBodyWithHeader($header),
            function (HtmlInterface $engine) {
                $engine->setContext($this->getContext());
            }
        );
    }

    /**
     * @test
     */
    public function generatePdfWithCustomFontTest()
    {
        // DejaVuSerif

        $body = /** @lang html */
            <<<BODY
<html><body>
<section><p style='color: blue'>This is the testing text that should be written in the PDF in <strong>Fira</strong></p></section>
</body></html>
BODY;
        $savePath = $this->pdfWithBody(
            $body,
            false,
            function (HtmlInterface $engine) {
                $engine->setStyles(" body, * {font-family: 'Fira';}");
                $engine->getContext()->addFontDirectoryPath(__DIR__ . '/../../../../Resources/FiraSans');
                $engine->getContext()->registerFont(
                    'fira',
                    array(
                        'R' => 'FiraSans-Thin.ttf',
                        'B' => 'FiraSans-Bold.ttf',
                    )
                );
            }
        );

        $this->assertPdfContainsRawContent($savePath, '+FiraSans-Thin');
    }

    /**
     * @test
     */
    public function generatePdfWithCustomContextAndFontTest()
    {
        $body = /** @lang html */
            <<<BODY
<html><body>
<section><p style='color: blue'>This is the testing text that should be written in the PDF in <strong>Fira</strong></p></section>
</body></html>
BODY;
        $savePath = $this->pdfWithBody(
            $body,
            false,
            function (HtmlInterface $engine) {
                $engine->setContext($this->getContext('fira'));
                $engine->setStyles(" body, * {font-family: 'Fira';}");
                $engine->getContext()->addFontDirectoryPath(__DIR__ . '/../../../../Resources/FiraSans');
                $engine->getContext()->registerFont(
                    'fira',
                    array(
                        'R' => 'FiraSans-Thin.ttf',
                        'B' => 'FiraSans-Bold.ttf',
                    )
                );
            }
        );

        $this->assertPdfContainsRawContent($savePath, '+FiraSans-Thin');
    }

    /**
     * @test
     */
    public function generateMultiPagePdfWithInlineHeaderAndCustomContextAndFontTest()
    {
        $header = 'This is the header';

        $this->pdfWithTextsCountAndBody(
            [$header => 4],
            $this->getLongMultiPageBodyWithHeader($header),
            function (HtmlInterface $engine) {
                $engine->setContext($this->getContext('fira'));
                $engine->setStyles(" body, * {font-family: 'Fira';}");
                $engine->getContext()->addFontDirectoryPath(__DIR__ . '/../../../../Resources/FiraSans');
                $engine->getContext()->registerFont(
                    'fira',
                    array(
                        'R' => 'FiraSans-Thin.ttf',
                        'B' => 'FiraSans-Bold.ttf',
                    )
                );
            }
        );
    }

    /**
     * @test
     */
    public function generateMultiPagePdfWithLongTextTest()
    {
        $footer = 'This is the footer';
        $this->pdfWithTextsCountAndBody(
            [$footer => 2],
            $this->getLongBodyTextHtml(),
            function (HtmlInterface $engine) use ($footer) {
                $engine->getContext()->SetHTMLFooter("<footer>$footer</footer>");
            }
        );
    }

    /**
     * @test
     */
    public function generatePdfWithLongTextAndManualPageBreakTest()
    {
        $header = 'This is the header';
        $footer = 'This is the footer';
        $section = $this->getLongBodyTextHtml();
        $body = sprintf(
            '<html><body>%s</body></html>',
            implode('<pagebreak />' . PHP_EOL, [$section, $section, $section,])
        );
        $this->pdfWithTextsCountAndBody(
            [$header => 6, $footer => 6],
            $body,
            function (HtmlInterface $engine) use ($header, $footer) {
                $engine->getContext()->SetHTMLHeader("<header>$header</header>");
                $engine->getContext()->SetHTMLFooter("<footer>$footer</footer>");
            }
        );
    }

    /**
     * @test
     */
    public function generatePdfWithLongTextAndManualPageBreakAndStylesTest()
    {
        $header = 'This is the header';
        $footer = 'This is the footer';
        $section = $this->getLongBodyTextHtml();
        $body = sprintf(
            '<html><body>%s</body></html>',
            implode('<pagebreak />' . PHP_EOL, [$section, $section, $section,])
        );
        $this->pdfWithTextsCountAndBody(
            [$header => 6, $footer => 6],
            $body,
            function (HtmlInterface $engine) use ($header, $footer) {
                $engine->setStyles('
@page {
    header: html_testHeader;
    footer: html_testFooter;
    margin-top: 26mm;
    margin-left: 28mm;
    margin-right: 17mm;
    margin-header: 5mm;
}');
                $engine->getContext()->DefHTMLHeaderByName('testHeader', "<header>$header</header>");
                $engine->getContext()->DefHTMLFooterByName('testFooter', "<footer>$footer</footer>");
            }
        );
    }

    /**
     * @param string $defaultFont
     * @return MpdfWrapper
     */
    protected function getContext($defaultFont = '')
    {
        return new MpdfWrapper(
            '',             // mode
            'A4',           // format
            14,             // default_font_size
            $defaultFont,   // default_font
            10,             // margin left
            10,             // margin right
            10,             // margin top
            10,             // margin bottom
            0,              // margin header
            0               // margin footer
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

    /**
     * @return string
     */
    protected function getLongBodyText()
    {
        return file_get_contents(__DIR__ . '/../../../../Resources/text.txt');
    }

    /**
     * @return string
     */
    protected function getLongBodyTextHtml()
    {
        return nl2br($this->getLongBodyText());
    }

    /**
     * @param $header
     * @return string
     */
    private function getLongMultiPageBodyWithHeader($header)
    {
        return /** @lang html */
            <<<BODY
                   <html><body>
<htmlpageheader name="header"><header>$header</header></htmlpageheader>
<sethtmlpageheader name="header" value="on" show-this-page="1" />
<section><p style='color: blue'>This is the testing text that should be written in the PDF on page 1</p></section>
<pagebreak />
<section><p style='color: red'>This should be written on page 2</p></section>
<pagebreak />
<section><p style='color: green'>{$this->getLongBodyTextHtml()}</p></section>
</body></html>
BODY;
    }
}
