<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Extractors\Standards;

trait FormatForeignTaxId20
{
    public function formatForeignTaxId(string $foreignTaxId): string
    {
        // codificar
        $foreignTaxId = htmlentities($foreignTaxId, ENT_XML1);
        // usar hasta un máximo de 20 posiciones
        $foreignTaxId = mb_substr($foreignTaxId, 0, 20);
        // crear un padding para establecer a 20 posiciones
        $padding = str_repeat('0', 20 - mb_strlen($foreignTaxId));
        return $padding . $foreignTaxId;
    }
}
