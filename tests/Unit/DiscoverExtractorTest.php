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

final class DiscoverExtractorTest extends DOMDocumentsTestCase
{
    public function testUniqueName(): void
    {
        $extractor = new DiscoverExtractor();
        $this->assertSame('discover', $extractor->uniqueName());
    }

    public function testGenericExtractorUsesDefaults(): void
    {
        $extractor = new DiscoverExtractor();
        $currentExpressionExtractors = $extractor->currentExpressionExtractors();
        $this->assertGreaterThan(0, count($currentExpressionExtractors));
        $this->assertContainsOnlyInstancesOf(ExpressionExtractorInterface::class, $currentExpressionExtractors);
    }

    public function testDefaultExtractorContainsKnownClasses(): void
    {
        $extractor = new DiscoverExtractor();
        $extractorClasses = array_map('get_class', $extractor->defaultExtractors());
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
        $extractor = new DiscoverExtractor();
        $this->assertFalse($extractor->matches($document));
    }

    public function testThrowExceptionOnUnmatchedDocument(): void
    {
        $document = new DOMDocument();
        $extractor = new DiscoverExtractor();
        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('Cannot discover any DiscoverExtractor that matches with document');
        $extractor->extract($document);
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
        $extractor = new DiscoverExtractor();
        $this->assertTrue($extractor->matches($document));
        $this->assertNotEmpty($extractor->extract($document));
    }

    /**
     * @param DOMDocument $document
     * @param string $type
     * @dataProvider providerExpressionOnValidDocuments
     */
    public function testExtractProducesTheSameResultsAsObtainAndFormat(DOMDocument $document, string $type): void
    {
        $extractor = new DiscoverExtractor();
        $values = $extractor->obtain($document);
        $expression = $extractor->format($values, $type);
        $expectedExpression = $extractor->extract($document);
        $this->assertSame($expression, $expectedExpression);
    }

    public function testFormatUsingNoType(): void
    {
        $extractor = new DiscoverExtractor();
        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('DiscoverExtractor requires type key with an extractor identifier');
        $extractor->format([]);
    }

    public function testFormatUsingCfdi33(): void
    {
        $extractor = new DiscoverExtractor();
        $this->assertNotEmpty($extractor->format([], 'CFDI33'));
    }
}
