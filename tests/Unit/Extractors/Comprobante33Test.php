<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit\Extractors;

use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\Extractors\Comprobante33;
use PhpCfdi\CfdiExpresiones\Tests\Unit\DOMDocumentsTestCase;

class Comprobante33Test extends DOMDocumentsTestCase
{
    public function testUniqueName(): void
    {
        $extrator = new Comprobante33();
        $this->assertSame('CFDI33', $extrator->uniqueName());
    }

    public function testMatchesCfdi33(): void
    {
        $document = $this->documentCfdi33();
        $extractor = new Comprobante33();
        $this->assertTrue($extractor->matches($document));
    }

    public function testExtractCfdi33(): void
    {
        $document = $this->documentCfdi33();
        $extractor = new Comprobante33();
        $expectedExpression = 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?'
            . 'id=CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC&re=POT9207213D6&rr=DIM8701081LA&tt=2010.01&fe=/OAgdg==';
        $this->assertSame($expectedExpression, $extractor->extract($document));
    }

    public function testNotMatchesCfdi32(): void
    {
        $document = $this->documentCfdi32();
        $extractor = new Comprobante33();
        $this->assertFalse($extractor->matches($document));
    }

    public function testExtractNotMatchesThrowException(): void
    {
        $document = $this->documentCfdi32();
        $extractor = new Comprobante33();
        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('The document is not a CFDI 3.3');
        $extractor->extract($document);
    }

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
        $extractor = new Comprobante33();
        $this->assertSame($expectedFormat, $extractor->formatTotal($input));
    }

    public function testFormatUsesFormatting(): void
    {
        $extractor = new Comprobante33();
        $expected33 = implode('', [
            'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx',
            '?id=CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC',
            '&re=Ñ&amp;A010101AAA',
            '&rr=Ñ&amp;A991231AA0',
            '&tt=1234.5678',
            '&fe=23456789',
        ]);
        $parameters = [
            'id' => 'CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC',
            're' => 'Ñ&A010101AAA',
            'rr' => 'Ñ&A991231AA0',
            'tt' => '1234.5678',
            'fe' => 'xxx23456789',
        ];
        $this->assertSame($expected33, $extractor->format($parameters));
    }

    public function testFormatRfcAmpersandOrTilde(): void
    {
        $extractor = new Comprobante33();
        $this->assertSame('ÑA&amp;A010101AA1', $extractor->formatRfc('ÑA&A010101AA1'));
    }

    /**
     * @testWith ["12345678", "12345678"]
     *           ["xxx12345678", "12345678"]
     *           ["1234", "1234"]
     */
    public function testFormatSelloTakesOnlyTheLastEightChars(string $input, string $expected): void
    {
        $extractor = new Comprobante33();
        $this->assertSame($expected, $extractor->formatSello($input));
    }
}
