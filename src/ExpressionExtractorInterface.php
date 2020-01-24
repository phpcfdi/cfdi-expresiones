<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones;

use DOMDocument;

interface ExpressionExtractorInterface
{
    /**
     * Extractor (implementor) unique name
     *
     * @return string
     */
    public function uniqueName(): string;

    /**
     * Check that the XML document matches with the extractor
     *
     * @param DOMDocument $document
     * @return bool
     */
    public function matches(DOMDocument $document): bool;

    /**
     * Obtain the relevant values from the given XML Document
     *
     * @param DOMDocument $document
     * @return array<string, string>
     */
    public function obtain(DOMDocument $document): array;

    /**
     * Format an expression based on given XML document
     *
     * @param DOMDocument $document
     * @return string
     */
    public function extract(DOMDocument $document): string;

    /**
     * Format an expression based on given values
     *
     * @param array<string, string> $values
     * @return string
     */
    public function format(array $values): string;
}
