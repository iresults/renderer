<?php
namespace Iresults\Renderer\Tests\Unit\Word;

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

// include __DIR__ . '/../../Classes/Iresults/Word/Renderer.php';


\Tx_Iresults::loadClassFile('Tx_Iresults_Model');
\Tx_Iresults::loadClassFile('Tx_Iresults_Helpers_ObjectHelper');
\Tx_CunddComposer_Autoloader::register();

#require_once __DIR__ . '/../../../cundd_composer/Classes/Autoloader.php';
#\Cundd\Composer\Autoloader::register();

// spl_autoload_register(function ($class) {
//	 require_once __DIR__ . '/../../Classes/' . str_replace('\\', '/', $class) . '.php';
// });

\Tx_Iresults::forceDebug();


use Iresults\Renderer\Word\Renderer;

class WordTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @test
	 */
	public function createDocument() {
		$savePath = tempnam(sys_get_temp_dir(), 'IWR');
		$document = new Renderer();
		$document->getContext()->addText('Hello world!');
		$document->save($savePath);
		$this->assertTrue(file_exists($savePath));
	}

	/**
	 * @test
	 */
	public function createDocumentFromTemplate() {
		$savePath = tempnam(sys_get_temp_dir(), 'IWR');
		$savePath = __DIR__ . '/test.docx';

		$document = Renderer::rendererWithTemplate(__DIR__ . '/reference.docx');

		$author = new \stdClass();
		$author->firstName = 'Ægir';
		$author->lastName = 'Jørgensen';

		$document->assign('author', $author);
		$document->getContext()->assign('time', date('r'));
		$document->getContext()->assign('some', 5);

		#$document->getContext()->setValue('name', 'Smöre ”Bröd”');
		#$document->getContext()->setValue('name', 'Daniel');
		$document->save($savePath);
		$this->assertTrue(file_exists($savePath));
	}
}

?>