<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\DiscoverExtractor;
use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\ExpressionExtractorInterface;
use PhpCfdi\CfdiExpresiones\Extractors\Comprobante32;
use PhpCfdi\CfdiExpresiones\Extractors\Comprobante33;
use PhpCfdi\CfdiExpresiones\Extractors\Comprobante40;
use PhpCfdi\CfdiExpresiones\Extractors\Retenciones10;
use PhpCfdi\CfdiExpresiones\Extractors\Retenciones20;

class DiscoverExtractorTest extends DOMDocumentsTestCase
{
    public function testUniqueName(): void
    {
        $extrator = new DiscoverExtractor();
        $this->assertSame('discover', $extrator->uniqueName());
    }

    public function testGenericExtratorUsesDefaults(): void
    {
        $extrator = new DiscoverExtractor();
        $currentExpressionExtractors = $extrator->currentExpressionExtractors();
        $this->assertGreaterThan(0, count($currentExpressionExtractors));
        $this->assertContainsOnlyInstancesOf(ExpressionExtractorInterface::class, $currentExpressionExtractors);
    }

    public function testDefaultExtractorContainsKnownClasses(): void
    {
        $extrator = new DiscoverExtractor();
        $extractorClasses = array_map('get_class', $extrator->defaultExtractors());
        $this->assertContains(Retenciones10::class, $extractorClasses);
        $this->assertContains(Retenciones20::class, $extractorClasses);
        $this->assertContains(Comprobante32::class, $extractorClasses);
        $this->assertContains(Comprobante33::class, $extractorClasses);
        $this->assertContains(Comprobante40::class, $extractorClasses);
        $this->assertCount(5, $extractorClasses);
    }

    public function testDontMatchUsingEmptyDocument(): void
    {
        $document = new DOMDocument();
        $extrator = new DiscoverExtractor();
        $this->assertFalse($extrator->matches($document));
    }

    public function testThrowExceptionOnUnmatchedDocument(): void
    {
        $document = new DOMDocument();
        $extrator = new DiscoverExtractor();
        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('Cannot discover any DiscoverExtractor that matches with document');
        $extrator->extract($document);
    }

    /** @return array<string, array{DOMDocument, string}> */
    public function providerExpressionOnValidDocuments(): array
    {
        return [
            'Cfdi40' => [$this->documentCfdi40(), 'CFDI40'],
            'Cfdi33' => [$this->documentCfdi33(), 'CFDI33'],
            'Cfdi32' => [$this->documentCfdi32(), 'CFDI32'],
            'Ret20Mexican' => [$this->documentRet20Mexican(), 'RET20'],
            'Ret20Foreign' => [$this->documentRet20Foreign(), 'RET20'],
            'Ret10Mexican' => [$this->documentRet10Mexican(), 'RET10'],
            'Ret10Foreign' => [$this->documentRet10Foreign(), 'RET10'],
        ];
    }

    /**
     * @param DOMDocument $document
     * @dataProvider providerExpressionOnValidDocuments
     */
    public function testExpressionOnValidDocuments(DOMDocument $document): void
    {
        $extrator = new DiscoverExtractor();
        $this->assertTrue($extrator->matches($document));
        $this->assertNotEmpty($extrator->extract($document));
    }

    /**
     * @param DOMDocument $document
     * @param string $type
     * @dataProvider providerExpressionOnValidDocuments
     */
    public function testExtractProducesTheSameResultsAsObtainAndFormat(DOMDocument $document, string $type): void
    {
        $extrator = new DiscoverExtractor();
        $values = $extrator->obtain($document);
        $expression = $extrator->format($values, $type);
        $expectedExpression = $extrator->extract($document);
        $this->assertSame($expression, $expectedExpression);
    }

    public function testFormatUsingNoType(): void
    {
        $extrator = new DiscoverExtractor();
        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('DiscoverExtractor requires type key with an extractor identifier');
        $extrator->format([]);
    }

    public function testFormatUsingCfdi33(): void
    {
        $extrator = new DiscoverExtractor();
        $this->assertNotEmpty($extrator->format([], 'CFDI33'));
    }
}
