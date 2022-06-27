<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Extractors\Standards;

trait FormatTotal10x6
{
    public function formatTotal(string $input): string
    {
        return str_pad(number_format(floatval($input), 6, '.', ''), 17, '0', STR_PAD_LEFT);
    }
}
