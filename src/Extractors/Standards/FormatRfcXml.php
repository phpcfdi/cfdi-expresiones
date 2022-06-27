<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Extractors\Standards;

trait FormatRfcXml
{
    public function formatRfc(string $rfc): string
    {
        return htmlentities($rfc, ENT_XML1);
    }
}
