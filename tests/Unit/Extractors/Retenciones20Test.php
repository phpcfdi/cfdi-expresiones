<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit\Extractors;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\Exceptions\AttributeNotFoundException;
use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\Extractors\Retenciones20;
use PhpCfdi\CfdiExpresiones\Tests\Unit\DOMDocumentsTestCase;

class Retenciones20Test extends DOMDocumentsTestCase
{
    public function testUniqueName(): void
    {
        $extrator = new Retenciones20();
        $this->assertSame('RET20', $extrator->uniqueName());
    }

    public function testMatchesRetenciones20(): void
    {
        $document = $this->documentRet20Foreign();
        $extractor = new Retenciones20();
        $this->assertTrue($extractor->matches($document));
    }

    public function testExtractRetenciones20Foreign(): void
    {
        $document = $this->documentRet20Foreign();
        $extractor = new Retenciones20();
        $expectedExpression = 'https://prodretencionverificacion.clouda.sat.gob.mx'
            . '?id=4E3DD8EA-5220-8C42-85A8-E37F9D7502F8'
            . '&re=AAA010101AAA'
            . '&nr=00000000001234567890'
            . '&tt=2000000.0'
            . '&fe=qsIe6w==';
        $this->assertSame($expectedExpression, $extractor->extract($document));
    }

    public function testExtractRetenciones20Mexican(): void
    {
        $document = $this->documentRet20Mexican();
        $extractor = new Retenciones20();
        $expectedExpression = 'https://prodretencionverificacion.clouda.sat.gob.mx'
            . '?id=4E3DD8EA-5220-8C42-85A8-E37F9D7502F8'
            . '&re=AAA010101AAA'
            . '&rr=SUL010720JN8'
            . '&tt=4076.73'
            . '&fe=qsIe6w==';
        $this->assertSame($expectedExpression, $extractor->extract($document));
    }

    /** @return array<string, array{DOMDocument}> */
    public function providerCfdiDifferentVersions(): array
    {
        return [
            'RET 1.0 Mexican' => [$this->documentRet10Mexican()],
            'RET 1.0 Foreign' => [$this->documentRet10Foreign()],
        ];
    }

    /** @dataProvider providerCfdiDifferentVersions */
    public function testNotMatchesCfdi(DOMDocument $document): void
    {
        $extractor = new Retenciones20();
        $this->assertFalse($extractor->matches($document));
    }

    /** @dataProvider providerCfdiDifferentVersions */
    public function testExtractNotMatchesThrowException(DOMDocument $document): void
    {
        $extractor = new Retenciones20();
        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('The document is not a RET 2.0');
        $extractor->extract($document);
    }

    public function testExtractWithoutReceptorThrowsException(): void
    {
        $document = new DOMDocument();
        $document->load($this->filePath('ret20-without-receptor-tax-id.xml'));
        $extractor = new Retenciones20();
        $this->expectException(AttributeNotFoundException::class);
        $this->expectExceptionMessage('RET 2.0 receiver tax id cannot be found');
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
        $extractor = new Retenciones20();
        $this->assertSame($expectedFormat, $extractor->formatTotal($input));
    }

    public function testFormatMexican(): void
    {
        $extractor = new Retenciones20();
        $expected = 'https://prodretencionverificacion.clouda.sat.gob.mx'
            . '?id=AAAAAAAA-BBBB-CCCC-DDDD-000000000000'
            . '&re=Ñ&amp;A010101AAA'
            . '&rr=Ñ&amp;A991231AA0'
            . '&tt=123456.78'
            . '&fe=qsIe6w==';
        $parameters = [
            'id' => 'AAAAAAAA-BBBB-CCCC-DDDD-000000000000',
            're' => 'Ñ&A010101AAA',
            'rr' => 'Ñ&A991231AA0',
            'tt' => '123456.78',
            'fe' => '...qsIe6w==',
        ];
        $this->assertSame($expected, $extractor->format($parameters));
    }

    public function testFormatForeign(): void
    {
        $extractor = new Retenciones20();
        $expected = 'https://prodretencionverificacion.clouda.sat.gob.mx'
            . '?id=AAAAAAAA-BBBB-CCCC-DDDD-000000000000'
            . '&re=Ñ&amp;A010101AAA'
            . '&nr=0000000000000000000X'
            . '&tt=123456.78'
            . '&fe=qsIe6w==';
        $parameters = [
            'id' => 'AAAAAAAA-BBBB-CCCC-DDDD-000000000000',
            're' => 'Ñ&A010101AAA',
            'nr' => 'X',
            'tt' => '123456.78',
            'fe' => '...qsIe6w==',
        ];
        $this->assertSame($expected, $extractor->format($parameters));
    }

    public function testFormatRfcAmpersandOrTilde(): void
    {
        $extractor = new Retenciones20();
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
        $extractor = new Retenciones20();
        $this->assertSame($expected, $extractor->formatForeignTaxId($input));
    }
}
