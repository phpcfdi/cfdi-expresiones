<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Internal;

use DOMDocument;
use DOMElement;
use LogicException;
use PhpCfdi\CfdiExpresiones\Exceptions\AttributeNotFoundException;
use PhpCfdi\CfdiExpresiones\Exceptions\ElementNotFoundException;

/**
 * Changes on this class does not create a major change on SEMVER
 * @internal
 */
class DOMHelper
{
    /** @var DOMDocument */
    private $document;

    public function __construct(DOMDocument $document)
    {
        $this->document = $document;
    }

    public function rootElement(): DOMElement
    {
        if (null === $this->document->documentElement) {
            throw new LogicException('DOMDocument does not have root element');
        }
        return $this->document->documentElement;
    }

    public function getAttribute(string ...$path): string
    {
        $value = $this->findAttribute(...$path);
        if (null === $value) {
            $attribute = array_pop($path);
            throw new AttributeNotFoundException(
                sprintf('Attribute %s@%s not found', implode('/', $path), $attribute)
            );
        }

        return $value;
    }

    public function findAttribute(string ...$path): ?string
    {
        $attribute = strval(array_pop($path));
        $element = $this->findElement(...$path);
        if (null === $element) {
            return null;
        }
        if (! $element->hasAttribute($attribute)) {
            return null;
        }
        return $element->getAttribute($attribute);
    }

    public function getElement(string ...$path): DOMElement
    {
        $element = $this->findElement(...$path);
        if (null === $element) {
            throw new ElementNotFoundException(
                sprintf('Element %s not found', implode('/', $path))
            );
        }

        return $element;
    }

    public function findElement(string ...$path): ?DOMElement
    {
        $name = strval(array_shift($path));
        $element = $this->rootElement();
        if ($name !== $element->nodeName) {
            return null;
        }

        $childElement = $element;
        foreach ($path as $childName) {
            $childElement = $this->findFirstChildByName($childElement, $childName);
            if (null === $childElement) {
                return null;
            }
        }

        return $childElement;
    }

    public function findFirstChildByName(DOMElement $parent, string $name): ?DOMElement
    {
        foreach ($parent->childNodes as $children) {
            if (! $children instanceof DOMElement) {
                continue;
            }
            if ($name === $children->nodeName) {
                return $children;
            }
        }

        return null;
    }
}
