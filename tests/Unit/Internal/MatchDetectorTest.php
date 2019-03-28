<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit\Internal;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\Internal\MatchDetector;
use PhpCfdi\CfdiExpresiones\Tests\TestCase;

class MatchDetectorTest extends TestCase
{
    public function testCheckPossitiveMatch(): void
    {
        $detector = new MatchDetector('http://example.com/books', 'b:books', 'version', 'v1');
        $document = new DOMDocument();
        $document->load($this->filePath('books.xml'));

        $this->assertTrue($detector->matches($document));
    }

    public function testCheckWithoutDocumentElement(): void
    {
        $detector = new MatchDetector('http://example.com/books', 'b:books', 'version', 'v1');
        $document = new DOMDocument();
        $this->assertFalse($detector->matches($document));

        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('Document does not have root element');
        $detector->check($document);
    }

    public function testCheckWithBadNamespaceUri(): void
    {
        $detector = new MatchDetector('http://example.com/foo', 'b:books', 'version', 'v1');
        $document = new DOMDocument();
        $document->load($this->filePath('books.xml'));
        $this->assertFalse($detector->matches($document));

        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('Document root element namespace does not match');
        $detector->check($document);
    }

    public function testCheckWithBadRootElementName(): void
    {
        $detector = new MatchDetector('http://example.com/books', 'b:foo', 'version', 'v1');
        $document = new DOMDocument();
        $document->load($this->filePath('books.xml'));
        $this->assertFalse($detector->matches($document));

        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('Document root element name does not match');
        $detector->check($document);
    }

    public function testCheckWithBadVersionAttributeName(): void
    {
        $detector = new MatchDetector('http://example.com/books', 'b:books', 'foo', 'v1');
        $document = new DOMDocument();
        $document->load($this->filePath('books.xml'));
        $this->assertFalse($detector->matches($document));

        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('Document root element version attribute does not match');
        $detector->check($document);
    }

    public function testCheckWithBadVersionAttributeValue(): void
    {
        $detector = new MatchDetector('http://example.com/books', 'b:books', 'version', 'foo');
        $document = new DOMDocument();
        $document->load($this->filePath('books.xml'));
        $this->assertFalse($detector->matches($document));

        $this->expectException(UnmatchedDocumentException::class);
        $this->expectExceptionMessage('Document root element version attribute does not match');
        $detector->check($document);
    }
}
