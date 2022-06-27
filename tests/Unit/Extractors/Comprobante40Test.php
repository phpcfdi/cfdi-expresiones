<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit\Extractors;

use DOMDocument;
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
        $expectedExpression = 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?'
            . 'id=04BF2854-FE7D-4377-9196-71248F060ABB&re=CSM190311AH6&rr=MCI7306249Y1&tt=459.36&fe=5tSZhA==';
        $this->assertSame($expectedExpression, $extractor->extract($document));
    }

    /** @return array<string, array{DOMDocument}> */
    public function providerCfdiDifferentVersions(): array
    {
        return [
            'CFDI 3.3' => [$this->documentCfdi33()],
            'CFDI 3.2' => [$this->documentCfdi32()],
        ];
    }

    /** @dataProvider providerCfdiDifferentVersions */
    public function testNotMatchesCfdi(DOMDocument $document): void
    {
        $extractor = new Comprobante40();
        $this->assertFalse($extractor->matches($document));
    }

    /** @dataProvider providerCfdiDifferentVersions */
    public function testExtractNotMatchesThrowException(DOMDocument $document): void
    {
        $extractor = new Comprobante40();
        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('The document is not a CFDI 4.0');
        $extractor->extract($document);
    }

    public function testFormatUsesFormatting(): void
    {
        $extractor = new Comprobante40();
        $expected40 = implode('', [
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
        $this->assertSame($expected40, $extractor->format($parameters));
    }
}
