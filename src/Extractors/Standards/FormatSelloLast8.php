<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Extractors\Standards;

trait FormatSelloLast8
{
    public function formatSello(string $sello): string
    {
        return substr($sello, -8);
    }
}
