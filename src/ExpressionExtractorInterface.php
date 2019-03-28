<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones;

use DOMDocument;

interface ExpressionExtractorInterface
{
    public function uniqueName(): string;

    public function matches(DOMDocument $document): bool;

    public function extract(DOMDocument $document): string;

    /**
     * Format an expression based on given values
     *
     * @param array<string, string> $values
     * @return string
     */
    public function format(array $values): string;
}
