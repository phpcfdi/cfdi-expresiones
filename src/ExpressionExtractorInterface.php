<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones;

use DOMDocument;

interface ExpressionExtractorInterface
{
    public function matches(DOMDocument $document): bool;

    public function extract(DOMDocument $document): string;
}
