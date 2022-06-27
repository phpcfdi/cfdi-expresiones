<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit\Extractors;

use DOMDocument;
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
        $extractor = new Comprobante32();
        $this->assertFalse($extractor->matches($document));
    }

    /** @dataProvider providerCfdiDifferentVersions */
    public function testExtractNotMatchesThrowException(DOMDocument $document): void
    {
        $extractor = new Comprobante32();
        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('The document is not a CFDI 3.2');
        $extractor->extract($document);
    }

    public function testFormatUsesFormatting(): void
    {
        $extractor = new Comprobante32();
        $expected32 = implode('', [
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
