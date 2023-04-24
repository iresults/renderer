<?php
declare(strict_types=1);

namespace Iresults\Renderer\Tests\Unit\Pdf\Engine\Html;

use Iresults\Renderer\Pdf\Engine\Html\HtmlInterface;
use Iresults\Renderer\Pdf\Wrapper\MpdfWrapper\MpdfWrapperInterface;
use Iresults\Renderer\Pdf\Wrapper\MpdfWrapperFactory;
use Iresults\Renderer\RendererInterface;
use Iresults\Renderer\Tests\Unit\Pdf\AssertionTrait;

trait HtmlEngineTestSuite
{
    use AssertionTrait;

    /**
     * @return HtmlInterface
     */
    abstract public function buildEngine(): RendererInterface;

    /**
     * @param string $suffix
     * @return string
     */
    abstract protected function getTempPath(string $suffix = 'pdf'): string;

    /**
     * Builds a temporary PDF and checks if the texts have the correct number of occurrences
     *
     * @param array         $textsAndCount   Texts and expected number of occurrences
     * @param callable|null $configureEngine Callback to configure the PDF
     * @return string Returns the save path
     */
    protected function pdfWithTextsCount(array $textsAndCount, callable $configureEngine = null): string
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
    protected function pdfWithTextsCountAndBody(array $texts, $body, callable $configureEngine = null): array
    {
        $configureEngine = $configureEngine ?: function () {
        };

        // Generate the PDF from the given HTML body and check assertions
        $pathFromHtmlBody = $this->pdfWithTextsCount(
            $texts,
            function (HtmlInterface $engine, $pdfPath) use ($configureEngine, $body) {
                $engine->setTemplate($body);
                $configureEngine($engine, $pdfPath);
            }
        );

        // Generate the PDF from the template file and check assertions
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
    protected function pdfWithBody($body, $useTemplateFile, callable $configureEngine = null): string
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
    public function generateSinglePagePdfTest(): void
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
    public function generateMultiPagePdfTest(): void
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
    public function generateMultiPagePdfWithHeaderTest(): void
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
    public function generateMultiPagePdfWithFooterTest(): void
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
    public function generateMultiPagePdfWithInlineHeaderTest(): void
    {
        $header = 'This is the header';
        $this->pdfWithTextsCountAndBody([$header => 4], $this->getLongMultiPageBodyWithHeader($header));
    }

    /**
     * @test
     */
    public function generateMultiPagePdfWithInlineHeaderAndCustomContextTest(): void
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
    public function generatePdfWithCustomFontTest(): void
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
                    [
                        'R' => 'FiraSans-Thin.ttf',
                        'B' => 'FiraSans-Bold.ttf',
                    ]
                );
            }
        );

        $this->assertPdfContainsRawContent($savePath, '+FiraSans-Thin');
    }

    /**
     * @test
     */
    public function generatePdfWithCustomContextAndFontTest(): void
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
                    [
                        'R' => 'FiraSans-Thin.ttf',
                        'B' => 'FiraSans-Bold.ttf',
                    ]
                );
            }
        );

        $this->assertPdfContainsRawContent($savePath, '+FiraSans-Thin');
    }

    /**
     * @test
     */
    public function generateMultiPagePdfWithInlineHeaderAndCustomContextAndFontTest(): void
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
                    [
                        'R' => 'FiraSans-Thin.ttf',
                        'B' => 'FiraSans-Bold.ttf',
                    ]
                );
            }
        );
    }

    /**
     * @test
     */
    public function generateMultiPagePdfWithLongTextTest(): void
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
    public function generatePdfWithLongTextAndManualPageBreakTest(): void
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
    public function generatePdfWithLongTextAndManualPageBreakAndStylesTest(): void
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
                $engine->setStyles(
                    '
@page {
    header: html_testHeader;
    footer: html_testFooter;
    margin-top: 26mm;
    margin-left: 28mm;
    margin-right: 17mm;
    margin-header: 5mm;
}'
                );
                $engine->getContext()->DefHTMLHeaderByName('testHeader', "<header>$header</header>");
                $engine->getContext()->DefHTMLFooterByName('testFooter', "<footer>$footer</footer>");
            }
        );
    }

    /**
     * @param string $defaultFont
     * @return MpdfWrapperInterface
     */
    protected function getContext($defaultFont = ''): MpdfWrapperInterface
    {
        $factory = new MpdfWrapperFactory();

        return $factory->build([
            'mode'              => '',
            'format'            => 'A4',
            'default_font_size' => 14,
            'default_font'      => $defaultFont,
            'margin_left'       => 10,
            'margin_right'      => 10,
            'margin_top'        => 10,
            'margin_bottom'     => 10,
            'margin_header'     => 0,
            'margin_footer'     => 0,
        ]);
    }

    /**
     * @param string $text
     * @return string
     */
    protected function getBodySinglePage(string $text): string
    {
        return "<html><body><section><p style='color: blue'>$text</p></section></body></html>";
    }

    /**
     * @param string ...$pages
     * @return string
     */
    protected function getBodyMultiPage(...$pages): string
    {
        $sections = [];
        foreach ($pages as $page) {
            $sections[] = "<section><p style='color: blue'>$page</p></section>";
        }

        return sprintf('<html><body>%s</body></html>', implode('<pagebreak />' . PHP_EOL, $sections));
    }

    /**
     * @return string
     */
    protected function getLongBodyText(): string
    {
        return file_get_contents(__DIR__ . '/../../../../Resources/text.txt');
    }

    /**
     * @return string
     */
    protected function getLongBodyTextHtml(): string
    {
        return nl2br($this->getLongBodyText());
    }

    /**
     * @param $header
     * @return string
     */
    private function getLongMultiPageBodyWithHeader($header): string
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
