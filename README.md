PDF
===

Using the HTML engine
---------------------

Add mpdf to the Composer JSON file

```json
{
    "require": {
        "mpdf/mpdf": "^8.0"
    }
}
```

```php
// Create the HTML Renderer instance
$pdfRenderer = \Iresults\Renderer\Pdf\Engine\Html\HtmlFactory::renderer();

$factory = new \Iresults\Renderer\Pdf\Wrapper\MpdfWrapperFactory();
// Set a different context object [optional]
$context = $factory->build([
    'mode'              => '',
    'format'            => 'A4',
    'default_font_size' => 10,
    'default_font'      => '',
    'margin_left'       => 0,
    'margin_right'      => 0,
    'margin_top'        => 40,
    'margin_bottom'     => 20,
    'margin_header'     => 0,
    'margin_footer'     => 0,
])
$pdfRenderer->setContext($context);


// Configuration: Additional fonts [optional]
$context->addFontDirectoryPath('/Path/To/Fonts/Directory');
$context->registerFont(
    'myriad',
    array(
        'R'  => 'MyriadPro-Regular.ttf',
        'I'  => 'MyriadPro-It.ttf',
        // Alternative bold styles:
        // 'B' => 'MyriadPro-Bold.ttf',
        // 'BI' => 'MyriadPro-BoldIt.ttf',
        'B'  => 'MyriadPro-Semibold.ttf',
        'BI' => 'MyriadPro-SemiboldIt.ttf',
    )
);

// Configuration: Define the header [optional]
$context->SetHTMLHeader('<header>Header</header>');

// Configuration: Define  the footer [optional]
$context->SetHTMLFooter('<footer>'.date('d.m.Y').'</footer>');
// or
$context->DefHTMLFooterByName('Footer', '<footer>'.date('d.m.Y').'</footer>');


// Set the template/content to render
$pdfRenderer->setTemplate($body);

// Define a path to save the PDF file at
$pdfRenderer->setSavePath($savePath);

// Add a stylesheet
$pdfRenderer->setStylesPath($styleSheet);

$pdfRenderer->render();


// Send the PDF to the browser
$pdfRenderer->output();
// or save it to the disk
$pdfRenderer->save();
```
