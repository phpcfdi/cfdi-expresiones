<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit\Extractors\Standards;

use PhpCfdi\CfdiExpresiones\Extractors\Standards\FormatForeignTaxId20;
use PhpCfdi\CfdiExpresiones\Tests\TestCase;

final class FormatForeignTaxId20Test extends TestCase
{
    /**
     * @testWith ["X", "0000000000000000000X"]
     *           ["12345678901234567890", "12345678901234567890"]
     *           ["12345678901234567890_1234", "12345678901234567890"]
     *           ["ÑÑÑ", "00000000000000000ÑÑÑ"]
     *           ["ÑÑÑÑÑÑÑÑÑÑÑÑÑÑÑÑÑÑÑÑ", "ÑÑÑÑÑÑÑÑÑÑÑÑÑÑÑÑÑÑÑÑ"]
     *           ["A&Z", "0000000000000A&amp;Z"]
     */
    public function testFormatForeignTaxId(string $input, string $expected): void
    {
        $extractor = new class () {
            use FormatForeignTaxId20;
        };

        $this->assertSame($expected, $extractor->formatForeignTaxId($input));
    }
}
