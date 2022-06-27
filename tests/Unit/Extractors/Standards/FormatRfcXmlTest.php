<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit\Extractors\Standards;

use PhpCfdi\CfdiExpresiones\Extractors\Standards\FormatRfcXml;
use PhpCfdi\CfdiExpresiones\Tests\TestCase;

final class FormatRfcXmlTest extends TestCase
{
    /**
     * @testWith ["AAA010101AAA", "AAA010101AAA"]
     *           ["AAAA010101AAA", "AAAA010101AAA"]
     *           ["ÑAAA010101AAA", "ÑAAA010101AAA"]
     *           ["&AAA010101AAA", "&amp;AAA010101AAA"]
     */
    public function testFormatRfc(string $input, string $expected): void
    {
        $extractor = new class () {
            use FormatRfcXml;
        };

        $this->assertSame($expected, $extractor->formatRfc($input));
    }
}
