<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Extractors;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\ExpressionExtractorInterface;
use PhpCfdi\CfdiExpresiones\Internal\DOMHelper;
use PhpCfdi\CfdiExpresiones\Internal\MatchDetector;

class Comprobante32 implements ExpressionExtractorInterface
{
    use Standards\FormatRfcXml;
    use Standards\FormatTotal10x6;

    /** @var MatchDetector */
    private $matchDetector;

    public function __construct()
    {
        $this->matchDetector = new MatchDetector('http://www.sat.gob.mx/cfd/3', 'cfdi:Comprobante', 'version', '3.2');
    }

    public function uniqueName(): string
    {
        return 'CFDI32';
    }

    public function matches(DOMDocument $document): bool
    {
        return $this->matchDetector->matches($document);
    }

    public function obtain(DOMDocument $document): array
    {
        if (! $this->matches($document)) {
            throw new UnmatchedDocumentException('The document is not a CFDI 3.2');
        }
        $helper = new DOMHelper($document);

        $uuid = $helper->getAttribute('cfdi:Comprobante', 'cfdi:Complemento', 'tfd:TimbreFiscalDigital', 'UUID');
        $rfcEmisor = $helper->getAttribute('cfdi:Comprobante', 'cfdi:Emisor', 'rfc');
        $rfcReceptor = $helper->getAttribute('cfdi:Comprobante', 'cfdi:Receptor', 'rfc');
        $total = $helper->getAttribute('cfdi:Comprobante', 'total');

        return [
            're' => $rfcEmisor,
            'rr' => $rfcReceptor,
            'tt' => $total,
            'id' => $uuid,
        ];
    }

    public function extract(DOMDocument $document): string
    {
        return $this->format($this->obtain($document));
    }

    public function format(array $values): string
    {
        return '?'
            . implode('&', [
                're=' . $this->formatRfc($values['re'] ?? ''),
                'rr=' . $this->formatRfc($values['rr'] ?? ''),
                'tt=' . $this->formatTotal($values['tt'] ?? ''),
                'id=' . ($values['id'] ?? ''),
            ]);
    }
}
