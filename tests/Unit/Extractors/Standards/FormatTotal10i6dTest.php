<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit\Extractors\Standards;

use PhpCfdi\CfdiExpresiones\Extractors\Standards\FormatTotal10x6;
use PhpCfdi\CfdiExpresiones\Tests\TestCase;

final class FormatTotal10i6dTest extends TestCase
{
    /**
     * Total must be 6 decimals and 17 total length zero padding on left
     *
     * @param string $input total cannot have more than 6 decimals as set in Anexo 20
     * @param string $expectedFormat
     * @testWith ["123.45",     "0000000123.450000"]
     *           ["0.123456",   "0000000000.123456"]
     *           ["0.1234561",  "0000000000.123456"]
     *           ["0.1234565",  "0000000000.123457"]
     *           ["1000.00000", "0000001000.000000"]
     *           ["0", "0000000000.000000"]
     *           ["0.00", "0000000000.000000"]
     *           ["", "0000000000.000000"]
     */
    public function testHowTotalMustBeFormatted(string $input, string $expectedFormat): void
    {
        $extractor = new class () {
            use FormatTotal10x6;
        };

        $this->assertSame($expectedFormat, $extractor->formatTotal($input));
    }
}
