<?php
declare(strict_types=1);

namespace Iresults\Renderer\Tests\Unit\Pdf;

use PHPUnit\Framework\Assert;

abstract class Assertion
{
    use AssertionTrait;

    /**
     * Tests if the given PDF contains the given texts X times
     *
     * <code>
     * $file = __DIR__ . '/../../Resources/code-pdf.pdf';
     * \Iresults\Renderer\Tests\Unit\Pdf\Assertion::assertPdfContainsTextsCount($file, ['Assertion' => 5]);
     * \Iresults\Renderer\Tests\Unit\Pdf\Assertion::assertPdfContainsTextsCount($file, ['Assertion' => -1]);
     * </code>
     *
     * @param string   $path
     * @param string[] $textsAndCount
     * @param string   $message
     * @throws \AssertionError if the assertion failed
     */
    public static function assertPdfContainsTextsCount($path, array $textsAndCount, $message = '')
    {
        $content = static::extractTextFromPdf($path);

        foreach ($textsAndCount as $text => $count) {
            Assert::assertIsNumeric($count);
            $count = intval($count);
            // If count is below 0 the count does not matter (text must occur at least once)
            if ($count < 0) {
                $message = $message ?: sprintf('Path "%s" does not contain "%s"', $path, $text);

                test_flight_assert((strpos($content, $text) !== false), $message);
            } else {
                $message = $message ?: sprintf(
                    'Path "%s" does not contain "%s" %d times but %d times',
                    $path,
                    $text,
                    $count,
                    substr_count($content, $text)
                );

                test_flight_assert_same($count, substr_count($content, $text), $message);
            }
        }
    }

    /**
     * Tests if the given PDF contains the given texts at least once
     *
     * <code>
     * $file = __DIR__ . '/../../Resources/code-pdf.pdf';
     * \Iresults\Renderer\Tests\Unit\Pdf\Assertion::assertPdfContainsTexts($file, ['Assertion'])
     * </code>
     *
     * @param string   $path
     * @param string[] $texts
     * @param string   $message
     * @throws \AssertionError if the assertion failed
     */
    public static function assertPdfContainsTexts($path, array $texts, $message = '')
    {
        $valuesArray = array_fill(0, count($texts), -1);

        static::assertPdfContainsTextsCount($path, array_combine(array_values($texts), $valuesArray), $message);
    }

    /**
     * Tests if the given PDF contains the given text
     *
     * <code>
     * $file = __DIR__ . '/../../Resources/code-pdf.pdf';
     * \Iresults\Renderer\Tests\Unit\Pdf\Assertion::assertPdfContainsText($file, 'Assertion')
     * </code>
     *
     * @param string $path
     * @param string $text
     * @param string $message
     * @throws \AssertionError if the assertion failed
     */
    public static function assertPdfContainsText($path, $text, $message = '')
    {
        static::assertPdfContainsTexts($path, [$text], $message);
    }

    /**
     * Tests if the given PDF contains the given text
     *
     * <code>
     * $file = __DIR__ . '/../../Resources/code-pdf.pdf';
     * \Iresults\Renderer\Tests\Unit\Pdf\Assertion::assertPdfContainsRawContent($file, '/FlateDecode')
     * </code>
     *
     * @param string $path
     * @param string $content
     * @param string $message
     * @throws \AssertionError if the assertion failed
     */
    public static function assertPdfContainsRawContent($path, $content, $message = '')
    {
        static::assertPdf($path, $message);

        $fileContent = file_get_contents($path);
        $message = $message ?: sprintf('Path "%s" does not contain "%s"', $path, $content);

        Assert::assertTrue((strpos($fileContent, $content) !== false), $message);
    }

    /**
     * Tests if the given PDF does not contain the given text
     *
     * <code>
     * $file = __DIR__ . '/../../Resources/code-pdf.pdf';
     * \Iresults\Renderer\Tests\Unit\Pdf\Assertion::assertPdfNotContainsRawContent($file, 'Some Text that should not occur')
     * </code>
     *
     * @param string $path
     * @param string $content
     * @param string $message
     * @throws \AssertionError if the assertion failed
     */
    public static function assertPdfNotContainsRawContent($path, $content, $message = '')
    {
        static::assertPdf($path, $message);

        $fileContent = file_get_contents($path);
        $message = $message ?: sprintf('Path "%s" does contain "%s"', $path, $content);

        test_flight_assert_false(strpos($fileContent, $content), $message);
    }

    /**
     * Assert that the given path is a valid PDF
     *
     * <code>
     * $file = __DIR__ . '/../../Resources/code-pdf.pdf';
     * \Iresults\Renderer\Tests\Unit\Pdf\Assertion::assertPdf($file);
     * $noPdfFile = __DIR__ . '/../../Resources/test-image.jpg';
     * test_flight_throws(function() {
     *  \Iresults\Renderer\Tests\Unit\Pdf\Assertion::assertPdf($noPdfFile);
     * })
     * </code>
     *
     * @param string $path
     * @param string $message
     * @throws \AssertionError if the assertion failed
     */
    public static function assertPdf($path, $message = '')
    {
        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('Path "%s" does not exist', $path));
        }

        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $path);
        finfo_close($fileInfo);

        if ('application/pdf' !== $mimeType) {
            if (!$message) {
                $message = sprintf('Path "%s" is not a valid PDF', $path);
            }
            throw new \AssertionError($message);
        }
    }

    /**
     * Extract text from the given PDF
     *
     * @param string $path
     * @return string
     */
    protected static function extractTextFromPdf($path)
    {
        static::assertPdf($path);

        $tempFile = tempnam(sys_get_temp_dir(), 'IWR') . '.txt';
        static::executeCommand('gs', '-sDEVICE=txtwrite', '-o', $tempFile, $path);

        return file_get_contents($tempFile);
    }

    /**
     * Executes a shell command and returns the output as array of lines
     *
     * @param string     $command
     * @param string|int ...$arguments
     * @return string[]
     * @throws \RuntimeException if the exit code is non-zero
     */
    protected static function executeCommand($command, ...$arguments)
    {
        $arguments = array_map('escapeshellarg', $arguments);
        array_unshift($arguments, $command);

        exec(implode(' ', $arguments), $output, $result);

        if ($result !== 0) {
            throw new \RuntimeException(
                sprintf('Command "%s" terminated with exit code %d: %s', $command, $result, implode(PHP_EOL, $output))
            );
        }

        return $output;
    }
}
