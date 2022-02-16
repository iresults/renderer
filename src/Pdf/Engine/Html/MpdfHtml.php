<?php
declare(strict_types=1);

namespace Iresults\Renderer\Pdf\Engine\Html;

use Iresults\Renderer\Pdf\Wrapper\MpdfWrapper\MpdfWrapperInterface;
use Iresults\Renderer\Pdf\Wrapper\MpdfWrapperFactory;
use function is_callable;
use function property_exists;

class MpdfHtml extends AbstractHtml
{
    /**
     * Render the PDF
     */
    protected function _render()
    {
        $this->getContext()->WriteHTML($this->getStyles(), 1);
        $this->getContext()->WriteHTML($this->getTemplate(), 2);
    }

    /**
     * Return the current rendering context (i.e. a section or page)
     *
     * @return MpdfWrapperInterface
     */
    public function getContext(): object
    {
        if (!$this->context) {
            $factory = new MpdfWrapperFactory();
            $this->context = $factory->build();
            if (is_callable([$this->context, 'SetDisplayMode'])) {
                $this->context->SetDisplayMode('fullpage');
            }
            if (property_exists($this->context, 'list_indent_first_level')) {
                // 1 or 0 - whether to indent the first level of a list
                $this->context->list_indent_first_level = 0;
            }
        }

        return $this->context;
    }
}
