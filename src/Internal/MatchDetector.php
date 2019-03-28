<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Internal;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;

/**
 * Changes on this class does not create a major change on SEMVER
 * @internal
 */
class MatchDetector
{
    /** @var string */
    public $namespaceUri;

    /** @var string */
    public $elementName;

    /** @var string */
    public $versionName;

    /** @var string */
    public $versionValue;

    public function __construct(string $namespaceUri, string $elementName, string $versionName, string $versionValue)
    {
        $this->namespaceUri = $namespaceUri;
        $this->elementName = $elementName;
        $this->versionName = $versionName;
        $this->versionValue = $versionValue;
    }

    public function check(DOMDocument $document): void
    {
        if (null === $document->documentElement) {
            throw new UnmatchedDocumentException('Document does not have root element');
        }
        if ($document->documentElement->namespaceURI !== $this->namespaceUri) {
            throw new UnmatchedDocumentException('Document root element namespace does not match');
        }
        if ($document->documentElement->nodeName !== $this->elementName) {
            throw new UnmatchedDocumentException('Document root element name does not match');
        }
        if ($document->documentElement->getAttribute($this->versionName) !== $this->versionValue) {
            throw new UnmatchedDocumentException('Document root element version attribute does not match');
        }
    }

    public function matches(DOMDocument $document): bool
    {
        try {
            $this->check($document);
        } catch (UnmatchedDocumentException $exception) {
            return false;
        }
        return true;
    }
}
