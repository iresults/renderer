<?php
declare(strict_types=1);

namespace Iresults\Renderer\Pdf\Wrapper;

use Iresults\Renderer\Pdf\Wrapper\MpdfWrapper\MpdfWrapperInterface;
use Iresults\Renderer\Pdf\Wrapper\MpdfWrapper\V6;
use Iresults\Renderer\Pdf\Wrapper\MpdfWrapper\V8;
use LogicException;
use function class_exists;

/**
 * @psalm-import-type MpdfConstructorConfiguration from MpdfWrapperInterface
 */
class MpdfWrapperFactory
{
    /**
     * @param MpdfConstructorConfiguration $config
     * @return MpdfWrapperInterface
     */
    public function build(array $config = []): MpdfWrapperInterface
    {
        if (class_exists(\Mpdf\Mpdf::class)) {
            return new V8($config);
        } elseif (class_exists(\mPDF::class)) {
            return new V6(...$config);
        } else {
            throw new LogicException('No mPDF version found');
        }
    }
}
