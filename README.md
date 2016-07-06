PDF
===

Using the HTML engine
---------------------

```php
// Create the HTML Renderer instance
$pdfRenderer = \Iresults\Renderer\Pdf\Engine\Html\HtmlFactory::renderer();

// Set a different context object [optional]
$context = new \Iresults\Renderer\Pdf\Wrapper\MpdfWrapper(
	'',     // mode
	'A4',   // format
	10,     // default_font_size
	'',     // default_font
	0,      // margin left
	0,      // margin right
	40,     // margin top
	20,     // margin bottom
	0,      // margin header
	0       // margin footer
);
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