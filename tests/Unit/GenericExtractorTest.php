<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\ExpressionExtractor;
use PhpCfdi\CfdiExpresiones\ExpressionExtractorInterface;

class GenericExtractorTest extends DOMDocumentsTestCase
{
    public function testGenericExtratorUsesDefaults(): void
    {
        $extrator = new ExpressionExtractor();
        $currentExpressionExtractors = $extrator->currentExpressionExtractors();
        $this->assertCount(3, $currentExpressionExtractors);
        $this->assertContainsOnlyInstancesOf(ExpressionExtractorInterface::class, $currentExpressionExtractors);
    }

    public function testDontMatchUsingEmptyDocument(): void
    {
        $document = new DOMDocument();
        $extrator = new ExpressionExtractor();
        $this->assertFalse($extrator->matches($document));
    }

    public function testThrowExceptionOnUnmatchedDocument(): void
    {
        $document = new DOMDocument();
        $extrator = new ExpressionExtractor();
        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('Cannot discover any ExpressionExtractor that matches with document');
        $extrator->extract($document);
    }

    public function providerExpressionOnValidDocuments()
    {
        return [
            'Cfdi33' => [$this->documentCfdi33()],
            'Cfdi32' => [$this->documentCfdi32()],
            'Ret10Mexican' => [$this->documentRet10Mexican()],
            'Ret10Foreign' => [$this->documentRet10Foreign()],
        ];
    }

    /**
     * @param DOMDocument $document
     * @dataProvider providerExpressionOnValidDocuments
     */
    public function testExpressionOnValidDocuments(DOMDocument $document): void
    {
        $extrator = new ExpressionExtractor();
        $this->assertTrue($extrator->matches($document));
        $this->assertNotEmpty($extrator->extract($document));
    }
}
