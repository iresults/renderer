<?php
declare(strict_types=1);

namespace Iresults\Renderer\Tests\Unit\Pdf\Engine;

use Iresults\Renderer\RendererInterface;
use Iresults\Renderer\Tests\Unit\Pdf\AssertionTrait;
use PHPUnit\Framework\TestCase;

abstract class AbstractEngineCase extends TestCase
{
    use AssertionTrait;

    /**
     * @return RendererInterface
     */
    abstract public function buildEngine(): RendererInterface;

    /**
     * @param string $suffix
     * @return string
     */
    protected function getTempPath(string $suffix = 'pdf'): string
    {
        return tempnam(sys_get_temp_dir(), 'IWR') . '.' . ltrim($suffix, '.');
    }
}
