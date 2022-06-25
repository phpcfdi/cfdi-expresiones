<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Extractors;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\ExpressionExtractorInterface;
use PhpCfdi\CfdiExpresiones\Internal\DOMHelper;
use PhpCfdi\CfdiExpresiones\Internal\MatchDetector;

class Comprobante40 implements ExpressionExtractorInterface
{
    /** @var MatchDetector */
    private $matchDetector;

    public function __construct()
    {
        $this->matchDetector = new MatchDetector('http://www.sat.gob.mx/cfd/4', 'cfdi:Comprobante', 'Version', '4.0');
    }

    public function uniqueName(): string
    {
        return 'CFDI40';
    }

    public function matches(DOMDocument $document): bool
    {
        return $this->matchDetector->matches($document);
    }

    public function obtain(DOMDocument $document): array
    {
        if (! $this->matches($document)) {
            throw new UnmatchedDocumentException('The document is not a CFDI 4.0');
        }

        $helper = new DOMHelper($document);
        $uuid = $helper->getAttribute('cfdi:Comprobante', 'cfdi:Complemento', 'tfd:TimbreFiscalDigital', 'UUID');
        $rfcEmisor = $helper->getAttribute('cfdi:Comprobante', 'cfdi:Emisor', 'Rfc');
        $rfcReceptor = $helper->getAttribute('cfdi:Comprobante', 'cfdi:Receptor', 'Rfc');
        $total = $helper->getAttribute('cfdi:Comprobante', 'Total');
        $sello = substr($helper->getAttribute('cfdi:Comprobante', 'Sello'), -8);

        return [
            'id' => $uuid,
            're' => $rfcEmisor,
            'rr' => $rfcReceptor,
            'tt' => $total,
            'fe' => $sello,
        ];
    }

    public function extract(DOMDocument $document): string
    {
        return $this->format($this->obtain($document));
    }

    public function format(array $values): string
    {
        return 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?'
            . implode('&', [
                'id=' . ($values['id'] ?? ''),
                're=' . (htmlentities($values['re'] ?? '', ENT_XML1)),
                'rr=' . (htmlentities($values['rr'] ?? '', ENT_XML1)),
                'tt=' . $this->formatTotal($values['tt'] ?? ''),
                'fe=' . ($values['fe'] ?? ''),
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
