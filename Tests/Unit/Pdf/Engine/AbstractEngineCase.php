<?php
/**
 * Created by PhpStorm.
 * User: cod
 * Date: 24.11.16
 * Time: 09:58
 */

namespace Iresults\Renderer\Tests\Unit\Pdf\Engine;


use Iresults\Renderer\RendererInterface;
use Iresults\Renderer\Tests\Unit\Pdf\AssertionTrait;

abstract class AbstractEngineCase extends \PHPUnit_Framework_TestCase
{
    use AssertionTrait;

    /**
     * @return RendererInterface
     */
    abstract public function buildEngine();

    /**
     * @param string $suffix
     * @return string
     */
    protected function getTempPath($suffix = 'pdf')
    {
        return tempnam(sys_get_temp_dir(), 'IWR') . '.' . ltrim($suffix, '.');
    }
}
