<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Extractors\Standards;

trait FormatTotal18x6
{
    public function formatTotal(string $input): string
    {
        $total = rtrim(number_format(floatval($input), 6, '.', ''), '0');
        if ('.' === substr($total, -1)) {
            $total = $total . '0'; // add trailing zero
        }
        return $total;
    }
}
