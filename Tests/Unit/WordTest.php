<?php
declare(strict_types=1);

namespace Iresults\Renderer\Tests\Unit;

use Iresults\Renderer\Word\Renderer;
use PhpOffice\PhpWord\PhpWord;
use PHPUnit\Framework\TestCase;

class WordTest extends TestCase
{
    /**
     * The path the test file will be saved to
     *
     * @type  string
     */
    protected $savePath;

    /**
     * Set up the test environment
     */
    public function setUp(): void
    {
        if (!class_exists(PHPWord::class)) {
            $this->markTestSkipped('Requires PHPWord to run');
        }

        $this->savePath = tempnam(sys_get_temp_dir(), 'IWR');
    }

    /**
     * Remove the temporary file
     */
    public function tearDown(): void
    {
        unlink($this->savePath);
    }

    /**
     * @test
     */
    public function createDocument()
    {
        $document = new Renderer();
        $document->getContext()->addText('Hello world!');
        $document->save($this->savePath);

        $this->assertTrue(file_exists($this->savePath));
    }

    /**
     * @test
     */
    public function createDocumentFromTemplate()
    {
        /**
         * The renderer that encapsulates the template instance
         *
         * @var \Iresults\Renderer\Word\Renderer
         */
        $document = Renderer::rendererWithTemplate(__DIR__ . '/reference.docx');

        // Assign variables through the context
        $document->getContext()->setValue('time', date('r'));
        $document->getContext()->setValue('some', 5);

        $author = new \stdClass();
        $author->firstName = 'Ægir';
        $author->lastName = 'Jørgensen';

        /*
         * The document should automatically forward unknown method calls to the
         * context or the driver
         */
        $document->setValue('author', $author);

        $document->save($this->savePath);
        $this->assertTrue(file_exists($this->savePath));
    }
}
