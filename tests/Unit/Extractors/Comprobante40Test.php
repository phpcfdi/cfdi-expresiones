<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit\Extractors;

use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\Extractors\Comprobante40;
use PhpCfdi\CfdiExpresiones\Tests\Unit\DOMDocumentsTestCase;

class Comprobante40Test extends DOMDocumentsTestCase
{
    public function testUniqueName(): void
    {
        $extrator = new Comprobante40();
        $this->assertSame('CFDI40', $extrator->uniqueName());
    }

    public function testMatchesCfdi40(): void
    {
        $document = $this->documentCfdi40();
        $extractor = new Comprobante40();
        $this->assertTrue($extractor->matches($document));
    }

    public function testExtractCfdi40(): void
    {
        $document = $this->documentCfdi40();
        $extractor = new Comprobante40();
        $expectedExpression = 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?' .
            'id=66933DFF-5507-5460-9EF0-9D0BB068A54A&re=EKU9003173C9&rr=AABF800614HI0&tt=2320.0&fe=yl2MKg==';
        $this->assertSame($expectedExpression, $extractor->extract($document));
    }

    public function testNotMatchesCfdi32(): void
    {
        $document = $this->documentCfdi32();
        $extractor = new Comprobante40();
        $this->assertFalse($extractor->matches($document));
    }

    public function testExtractNotMatchesThrowException(): void
    {
        $document = $this->documentCfdi32();
        $extractor = new Comprobante40();
        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('The document is not a CFDI 4.0');
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
        $extractor = new Comprobante40();
        $this->assertSame($expectedFormat, $extractor->formatTotal($input));
    }

    public function testFormatCfdi40RfcWithAmpersand(): void
    {
        $extractor = new Comprobante40();
        $expected40 = implode([
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
            'fe' => '23456789',
        ];
        $this->assertSame($expected40, $extractor->format($parameters));
    }
}
