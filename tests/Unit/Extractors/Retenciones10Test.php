<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit\Extractors;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\Exceptions\AttributeNotFoundException;
use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\Extractors\Retenciones10;
use PhpCfdi\CfdiExpresiones\Tests\Unit\DOMDocumentsTestCase;

class Retenciones10Test extends DOMDocumentsTestCase
{
    public function testUniqueName(): void
    {
        $extrator = new Retenciones10();
        $this->assertSame('RET10', $extrator->uniqueName());
    }

    public function testMatchesRetenciones10(): void
    {
        $document = $this->documentRet10Foreign();
        $extractor = new Retenciones10();
        $this->assertTrue($extractor->matches($document));
    }

    public function testExtractRetenciones10Foreign(): void
    {
        $document = $this->documentRet10Foreign();
        $extractor = new Retenciones10();
        $expectedExpression = '?re=AAA010101AAA&nr=00000000001234567890&tt=0002000000.000000'
            . '&id=fc1b47b2-42f3-4ca2-8587-36e0a216c4d5';
        $this->assertSame($expectedExpression, $extractor->extract($document));
    }

    public function testExtractRetenciones10Mexican(): void
    {
        $document = $this->documentRet10Mexican();
        $extractor = new Retenciones10();
        $expectedExpression = '?re=AAA010101AAA&rr=SUL010720JN8&tt=0002000000.000000'
            . '&id=fc1b47b2-42f3-4ca2-8587-36e0a216c4d5';
        $this->assertSame($expectedExpression, $extractor->extract($document));
    }

    /** @return array<string, array{DOMDocument}> */
    public function providerCfdiDifferentVersions(): array
    {
        return [
            'CFDI 4.0' => [$this->documentCfdi40()],
            'CFDI 3.3' => [$this->documentCfdi33()],
        ];
    }

    /** @dataProvider providerCfdiDifferentVersions */
    public function testNotMatchesCfdi(DOMDocument $document): void
    {
        $extractor = new Retenciones10();
        $this->assertFalse($extractor->matches($document));
    }

    /** @dataProvider providerCfdiDifferentVersions */
    public function testExtractNotMatchesThrowException(DOMDocument $document): void
    {
        $extractor = new Retenciones10();
        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('The document is not a RET 1.0');
        $extractor->extract($document);
    }

    public function testExtractWithoutReceptorThrowsException(): void
    {
        $document = new DOMDocument();
        $document->load($this->filePath('ret10-without-receptor-tax-id.xml'));
        $extractor = new Retenciones10();
        $this->expectException(AttributeNotFoundException::class);
        $this->expectExceptionMessage('RET 1.0 receiver tax id cannot be found');
        $extractor->extract($document);
    }

    /**
     * RET 1.0 total must be 6 decimals and 17 total length zero padding on left
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
        $extractor = new Retenciones10();
        $this->assertSame($expectedFormat, $extractor->formatTotal($input));
    }

    public function testFormatMexican(): void
    {
        $extractor = new Retenciones10();
        $expected = implode('', [
            '?re=Ñ&amp;A010101AAA',
            '&rr=Ñ&amp;A991231AA0',
            '&tt=0002000000.000000',
            '&id=fc1b47b2-42f3-4ca2-8587-36e0a216c4d5',
        ]);
        $parameters = [
            're' => 'Ñ&A010101AAA',
            'rr' => 'Ñ&A991231AA0',
            'tt' => '2000000.00',
            'id' => 'fc1b47b2-42f3-4ca2-8587-36e0a216c4d5',
        ];
        $this->assertSame($expected, $extractor->format($parameters));
    }

    public function testFormatForeign(): void
    {
        $extractor = new Retenciones10();
        $expected = implode('', [
            '?re=ÑA&amp;010101AA1',
            '&nr=0000000000000000000X',
            '&tt=0000012345.670000',
            '&id=AAAAAAAA-BBBB-CCCC-DDDD-000000000000',
        ]);
        $parameters = [
            're' => 'ÑA&010101AA1',
            'nr' => 'X',
            'id' => 'AAAAAAAA-BBBB-CCCC-DDDD-000000000000',
            'tt' => '12345.67',
        ];
        $this->assertSame($expected, $extractor->format($parameters));
    }

    public function testFormatRfcAmpersandOrTilde(): void
    {
        $extractor = new Retenciones10();
        $this->assertSame('ÑA&amp;A010101AA1', $extractor->formatRfc('ÑA&A010101AA1'));
    }

    /**
     * @testWith ["X", "0000000000000000000X"]
     *           ["12345678901234567890", "12345678901234567890"]
     *           ["12345678901234567890_1234", "12345678901234567890"]
     *           ["ÑÑÑ", "00000000000000000ÑÑÑ"]
     *           ["A&Z", "0000000000000A&amp;Z"]
     */
    public function testFormatForeignTaxId(string $input, string $expected): void
    {
        $extractor = new Retenciones10();
        $this->assertSame($expected, $extractor->formatForeignTaxId($input));
    }
}
