<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones;

use DOMDocument;

interface ExpressionExtractorInterface
{
    /**
     * Extractor (implementor) unique name
     */
    public function uniqueName(): string;

    /**
     * Check that the XML document matches with the extractor
     */
    public function matches(DOMDocument $document): bool;

    /**
     * Obtain the relevant values from the given XML Document
     *
     * @return array<string, string>
     */
    public function obtain(DOMDocument $document): array;

    /**
     * Format an expression based on given XML document
     */
    public function extract(DOMDocument $document): string;

    /**
     * Format an expression based on given values
     *
     * @param array<string, string> $values
     */
    public function format(array $values): string;
}
