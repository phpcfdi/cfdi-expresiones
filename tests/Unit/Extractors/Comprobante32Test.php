<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit\Extractors;

use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\Extractors\Comprobante32;
use PhpCfdi\CfdiExpresiones\Tests\Unit\DOMDocumentsTestCase;

class Comprobante32Test extends DOMDocumentsTestCase
{
    public function testUniqueName(): void
    {
        $extrator = new Comprobante32();
        $this->assertSame('CFDI32', $extrator->uniqueName());
    }

    public function testMatchesCfdi32(): void
    {
        $document = $this->documentCfdi32();
        $extractor = new Comprobante32();
        $this->assertTrue($extractor->matches($document));
    }

    public function testExtractCfdi32(): void
    {
        $document = $this->documentCfdi32();
        $extractor = new Comprobante32();
        $expectedExpression = '?re=CTO021007DZ8&rr=XAXX010101000&tt=0000004685.000000'
            . '&id=80824F3B-323E-407B-8F8E-40D83FE2E69F';
        $this->assertSame($expectedExpression, $extractor->extract($document));
    }

    public function testNotMatchesCfdi33(): void
    {
        $document = $this->documentCfdi33();
        $extractor = new Comprobante32();
        $this->assertFalse($extractor->matches($document));
    }

    public function testExtractNotMatchesThrowException(): void
    {
        $document = $this->documentCfdi33();
        $extractor = new Comprobante32();
        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('The document is not a CFDI 3.2');
        $extractor->extract($document);
    }

    /**
     * CFDI 3.2 total must be 6 decimals and 17 total length zero padding on left
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
        $extractor = new Comprobante32();
        $this->assertSame($expectedFormat, $extractor->formatTotal($input));
    }

    public function testFormatCfdi32RfcWithAmpersand(): void
    {
        $extractor = new Comprobante32();
        $expected32 = implode([
            '?re=Ñ&amp;A010101AAA',
            '&rr=Ñ&amp;A991231AA0',
            '&tt=0000001234.567800',
            '&id=CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC',
        ]);
        $parameters = [
            're' => 'Ñ&A010101AAA',
            'rr' => 'Ñ&A991231AA0',
            'tt' => '1234.5678',
            'id' => 'CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC',
        ];
        $this->assertSame($expected32, $extractor->format($parameters));
    }
}
