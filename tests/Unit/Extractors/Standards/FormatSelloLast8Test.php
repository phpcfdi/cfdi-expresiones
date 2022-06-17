<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit\Extractors\Standards;

use PhpCfdi\CfdiExpresiones\Extractors\Standards\FormatSelloLast8;
use PhpCfdi\CfdiExpresiones\Tests\TestCase;

final class FormatSelloLast8Test extends TestCase
{
    /**
     * @testWith ["12345678", "12345678"]
     *           ["xxx12345678", "12345678"]
     *           ["1234", "1234"]
     */
    public function testFormatSelloTakesOnlyTheLastEightChars(string $input, string $expected): void
    {
        $extractor = new class () {
            use FormatSelloLast8;
        };

        $this->assertSame($expected, $extractor->formatSello($input));
    }
}
