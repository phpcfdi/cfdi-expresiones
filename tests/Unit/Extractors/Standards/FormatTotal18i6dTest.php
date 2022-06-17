<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit\Extractors\Standards;

use PhpCfdi\CfdiExpresiones\Extractors\Standards\FormatTotal18x6;
use PhpCfdi\CfdiExpresiones\Tests\TestCase;

final class FormatTotal18i6dTest extends TestCase
{
    /**
     * @param string $input total cannot have more than 6 decimals as set in Anexo 20
     * @param string $expectedFormat
     * @testWith ["123.45", "123.45"]
     *           ["0.123456", "0.123456"]
     *           ["0.1234561", "0.123456"]
     *           ["0.1234565", "0.123457"]
     *           ["1000.00000", "1000.0"]
     *           ["0", "0.0"]
     *           ["0.00", "0.0"]
     *           ["", "0.0"]
     */
    public function testHowTotalMustBeFormatted(string $input, string $expectedFormat): void
    {
        $extractor = new class () {
            use FormatTotal18x6;
        };

        $this->assertSame($expectedFormat, $extractor->formatTotal($input));
    }
}
