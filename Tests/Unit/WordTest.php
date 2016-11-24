<?php

/*
 * Copyright (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *					Daniel Corn <cod@iresults.li>, iresults
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @copyright  Copyright (c) 2013
 * @license	http://opensource.org/licenses/MIT MIT
 * @version	1.0.0
 */

namespace Iresults\Renderer\Tests\Unit;

// include __DIR__ . '/../../Classes/Iresults/Word/Renderer.php';



#require_once __DIR__ . '/../../../cundd_composer/Classes/Autoloader.php';
#\Cundd\Composer\Autoloader::register();

// spl_autoload_register(function ($class) {
//	 require_once __DIR__ . '/../../Classes/' . str_replace('\\', '/', $class) . '.php';
// });



use Iresults\Renderer\Word\Renderer;
use org\bovigo\vfs\vfsStream;

class WordTest extends \PHPUnit_Framework_TestCase
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
    public function setUp()
    {
        if (!class_exists('PHPWord')) {
            $this->markTestSkipped('Requires PHPWord to run');
        }

        $this->savePath = tempnam(sys_get_temp_dir(), 'IWR');
    }

    /**
     * Remove the temporary file
     */
    public function tearDown()
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
        $document->getContext()->assign('time', date('r'));
        $document->getContext()->assign('some', 5);

        $author = new \stdClass();
        $author->firstName = 'Ægir';
        $author->lastName = 'Jørgensen';

        /*
         * The document should automatically forward unknown method calls to the
         * context or the driver
         */
        $document->assign('author', $author);

        $document->save($this->savePath);
        $this->assertTrue(file_exists($this->savePath));
    }
}
