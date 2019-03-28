<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit\Internal;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\Exceptions\AttributeNotFoundException;
use PhpCfdi\CfdiExpresiones\Exceptions\ElementNotFoundException;
use PhpCfdi\CfdiExpresiones\Internal\DOMHelper;
use PhpCfdi\CfdiExpresiones\Tests\TestCase;

class DOMHelperTest extends TestCase
{
    public function testFailsUsingDocumentWithoutRootElement(): void
    {
        $document = new DOMDocument();
        $helper = new DOMHelper($document);
        $this->expectException(\LogicException::class);
        $helper->rootElement();
    }

    public function testReturnsRootElement(): void
    {
        $document = new DOMDocument();
        $helper = new DOMHelper($document);
        $document->load($this->filePath('books.xml'));

        $this->assertSame($document->documentElement, $helper->rootElement());
    }

    public function testReturnsFindRootElement(): void
    {
        $document = new DOMDocument();
        $helper = new DOMHelper($document);
        $document->load($this->filePath('books.xml'));

        $element = $helper->findElement('b:books');
        $this->assertSame($document->documentElement, $element);
    }

    public function testReturnsNullFindingInvalidRootElement(): void
    {
        $document = new DOMDocument();
        $helper = new DOMHelper($document);
        $document->load($this->filePath('books.xml'));

        $element = $helper->findElement('b:foo');
        $this->assertNull($element);
    }

    public function testThrowsExceptionGettingInvalidRootElement(): void
    {
        $document = new DOMDocument();
        $helper = new DOMHelper($document);
        $document->load($this->filePath('books.xml'));

        $this->expectException(ElementNotFoundException::class);
        $this->expectExceptionMessage('Element b:foo not found');
        $helper->getElement('b:foo');
    }

    public function testFindingInDepth(): void
    {
        $document = new DOMDocument();
        $helper = new DOMHelper($document);
        $document->load($this->filePath('books.xml'));

        $element = $helper->findElement('b:books', 'b:library', 't:topic', 'b:book');
        if (null === $element) {
            $this->fail('Expected to exists element was not found');
            return;
        }
        $this->assertSame('Carlos C Soto', $element->getAttribute('author'));
    }

    public function testGettingInDepth(): void
    {
        $document = new DOMDocument();
        $helper = new DOMHelper($document);
        $document->load($this->filePath('books.xml'));

        $element = $helper->getElement('b:books', 'b:library', 't:topic', 'b:book');
        $this->assertSame('Carlos C Soto', $element->getAttribute('author'));
    }

    public function testThrowsExceptionGettingInvalidElementInDepth(): void
    {
        $document = new DOMDocument();
        $helper = new DOMHelper($document);
        $document->load($this->filePath('books.xml'));

        $this->expectException(ElementNotFoundException::class);
        $this->expectExceptionMessage('Element b:books/b:library/t:topic/b:book/b:foo not found');
        $helper->getElement('b:books', 'b:library', 't:topic', 'b:book', 'b:foo');
    }

    public function testThrowsExceptionGettingInvalidAttributeInDepth(): void
    {
        $document = new DOMDocument();
        $helper = new DOMHelper($document);
        $document->load($this->filePath('books.xml'));

        $this->expectException(AttributeNotFoundException::class);
        $this->expectExceptionMessage('Attribute b:books/b:library/t:topic/b:book@foo not found');
        $helper->getAttribute('b:books', 'b:library', 't:topic', 'b:book', 'foo');
    }

    public function testReturnsNullFindingInvalidAttributeInDepth(): void
    {
        $document = new DOMDocument();
        $helper = new DOMHelper($document);
        $document->load($this->filePath('books.xml'));

        $this->assertNull($helper->findAttribute('b:books', 'b:library', 't:topic', 'b:book', 'foo'));
    }

    public function testFindAttributeInDepth(): void
    {
        $document = new DOMDocument();
        $helper = new DOMHelper($document);
        $document->load($this->filePath('books.xml'));

        $attribute = $helper->findAttribute('b:books', 'b:library', 't:topic', 'b:book', 'author');
        $this->assertSame('Carlos C Soto', $attribute);
    }

    public function testGetAttributeInDepth(): void
    {
        $document = new DOMDocument();
        $helper = new DOMHelper($document);
        $document->load($this->filePath('books.xml'));

        $attribute = $helper->getAttribute('b:books', 'b:library', 't:topic', 'b:book', 'author');
        $this->assertSame('Carlos C Soto', $attribute);
    }
}
