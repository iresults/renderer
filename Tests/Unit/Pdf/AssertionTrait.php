<?php
declare(strict_types=1);

namespace Iresults\Renderer\Tests\Unit\Pdf;

trait AssertionTrait
{
    /**
     * Tests if the given PDF contains the given text
     *
     * @param string   $path
     * @param string[] $textsAndCount
     * @param string   $message
     * @throws \AssertionError if the assertion failed
     */
    public static function assertPdfContainsTextsCount($path, array $textsAndCount, $message = '')
    {
        Assertion::assertPdfContainsTextsCount($path, $textsAndCount, $message);
    }

    /**
     * Tests if the given PDF contains the given text
     *
     * @param string   $path
     * @param string[] $texts
     * @param string   $message
     * @throws \AssertionError if the assertion failed
     */
    public static function assertPdfContainsTexts($path, array $texts, $message = '')
    {
        Assertion::assertPdfContainsTexts($path, $texts, $message);
    }

    /**
     * Tests if the given PDF contains the given text
     *
     * @param string $path
     * @param string $text
     * @param string $message
     * @throws \AssertionError if the assertion failed
     */
    public static function assertPdfContainsText($path, $text, $message = '')
    {
        Assertion::assertPdfContainsTexts($path, (array)$text, $message);
    }

    /**
     * Assert that the given path is a valid PDF
     *
     * @param string $path
     * @param string $message
     * @throws \AssertionError if the assertion failed
     */
    public static function assertPdf($path, $message = '')
    {
        Assertion::assertPdf($path, $message);
    }

    /**
     * Tests if the given PDF contains the given raw content
     *
     * @param string $path
     * @param string $content
     * @param string $message
     * @throws \AssertionError if the assertion failed
     */
    public static function assertPdfContainsRawContent($path, $content, $message = '')
    {
        Assertion::assertPdfContainsRawContent($path, $content, $message);
    }

    /**
     * Tests if the given PDF does not contain the given raw content
     *
     * @param string $path
     * @param string $content
     * @param string $message
     * @throws \AssertionError if the assertion failed
     */
    public static function assertPdfNotContainsRawContent($path, $content, $message = '')
    {
        Assertion::assertPdfNotContainsRawContent($path, $content, $message);
    }
}
