<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit\Extractors;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\Extractors\Comprobante33;
use PhpCfdi\CfdiExpresiones\Tests\Unit\DOMDocumentsTestCase;

class Comprobante33Test extends DOMDocumentsTestCase
{
    public function testUniqueName(): void
    {
        $extractor = new Comprobante33();
        $this->assertSame('CFDI33', $extractor->uniqueName());
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

    /** @return array<string, array{DOMDocument}> */
    public function providerCfdiDifferentVersions(): array
    {
        return [
            'CFDI 4.0' => [$this->documentCfdi40()],
            'CFDI 3.2' => [$this->documentCfdi32()],
        ];
    }

    /** @dataProvider providerCfdiDifferentVersions */
    public function testNotMatchesCfdi(DOMDocument $document): void
    {
        $extractor = new Comprobante33();
        $this->assertFalse($extractor->matches($document));
    }

    /** @dataProvider providerCfdiDifferentVersions */
    public function testExtractNotMatchesThrowException(DOMDocument $document): void
    {
        $extractor = new Comprobante33();
        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('The document is not a CFDI 3.3');
        $extractor->extract($document);
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
}
