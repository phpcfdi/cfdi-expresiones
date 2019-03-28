<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Extractors;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\ExpressionExtractorInterface;
use PhpCfdi\CfdiExpresiones\Internal\DOMHelper;
use PhpCfdi\CfdiExpresiones\Internal\MatchDetector;

class Comprobante33 implements ExpressionExtractorInterface
{
    /** @var MatchDetector */
    private $matchDetector;

    public function __construct()
    {
        $this->matchDetector = new MatchDetector('http://www.sat.gob.mx/cfd/3', 'cfdi:Comprobante', 'Version', '3.3');
    }

    public function matches(DOMDocument $document): bool
    {
        return $this->matchDetector->matches($document);
    }

    public function extract(DOMDocument $document): string
    {
        if (! $this->matches($document)) {
            throw new UnmatchedDocumentException('The document is not a CFDI 3.3');
        }

        $helper = new DOMHelper($document);
        $uuid = $helper->getAttribute('cfdi:Comprobante', 'cfdi:Complemento', 'tfd:TimbreFiscalDigital', 'UUID');
        $rfcEmisor = $helper->getAttribute('cfdi:Comprobante', 'cfdi:Emisor', 'Rfc');
        $rfcReceptor = $helper->getAttribute('cfdi:Comprobante', 'cfdi:Receptor', 'Rfc');
        $totalComprobante = $helper->getAttribute('cfdi:Comprobante', 'Total');
        $sello = substr($helper->getAttribute('cfdi:Comprobante', 'Sello'), -8);

        $total = $this->formatTotal($totalComprobante);

        return 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?' . implode('&', [
            'id=' . $uuid,
            're=' . $rfcEmisor,
            'rr=' . $rfcReceptor,
            'tt=' . $total,
            'fe=' . substr($sello, -8),
        ]);
    }

    public function formatTotal(string $input): string
    {
        $total = rtrim(number_format(floatval($input), 6, '.', ''), '0');
        if ('.' === substr($total, -1, 1)) {
            $total = $total . '0'; // add trailing zero
        }
        return $total;
    }
}
