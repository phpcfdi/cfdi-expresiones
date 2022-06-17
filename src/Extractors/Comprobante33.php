<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Extractors;

use PhpCfdi\CfdiExpresiones\ExpressionExtractorInterface;
use PhpCfdi\CfdiExpresiones\Extractors\Standards\Comprobante20170701;
use PhpCfdi\CfdiExpresiones\Internal\MatchDetector;

/**
 * This class is using the CFDI Standard 2017-07-01. It's the same for CFDI 3.3 & 4.0.
 */
class Comprobante33 extends Comprobante20170701 implements ExpressionExtractorInterface
{
    public function __construct()
    {
        parent::__construct(
            new MatchDetector('http://www.sat.gob.mx/cfd/3', 'cfdi:Comprobante', 'Version', '3.3'),
            'The document is not a CFDI 3.3'
        );
    }

    public function uniqueName(): string
    {
        return 'CFDI33';
    }
}
