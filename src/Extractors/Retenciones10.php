<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Extractors;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\Exceptions\AttributeNotFoundException;
use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\ExpressionExtractorInterface;
use PhpCfdi\CfdiExpresiones\Internal\DOMHelper;
use PhpCfdi\CfdiExpresiones\Internal\MatchDetector;

class Retenciones10 implements ExpressionExtractorInterface
{
    use Standards\FormatForeignTaxId20;
    use Standards\FormatRfcXml;
    use Standards\FormatTotal10x6;

    /** @var MatchDetector */
    private $matchDetector;

    public function __construct()
    {
        $this->matchDetector = new MatchDetector(
            'http://www.sat.gob.mx/esquemas/retencionpago/1',
            'retenciones:Retenciones',
            'Version',
            '1.0'
        );
    }

    public function uniqueName(): string
    {
        return 'RET10';
    }

    public function matches(DOMDocument $document): bool
    {
        return $this->matchDetector->matches($document);
    }

    public function obtain(DOMDocument $document): array
    {
        if (! $this->matches($document)) {
            throw new UnmatchedDocumentException('The document is not a RET 1.0');
        }

        $helper = new DOMHelper($document);

        $uuid = $helper->getAttribute(
            'retenciones:Retenciones',
            'retenciones:Complemento',
            'tfd:TimbreFiscalDigital',
            'UUID'
        );

        $rfcEmisor = $helper->getAttribute('retenciones:Retenciones', 'retenciones:Emisor', 'RFCEmisor');
        [$rfcReceptorKey, $rfcReceptor] = $this->obtainReceptorValues($helper);
        $total = $helper->getAttribute('retenciones:Retenciones', 'retenciones:Totales', 'montoTotOperacion');

        return [
            're' => $rfcEmisor,
            $rfcReceptorKey => $rfcReceptor,
            'tt' => $this->formatTotal($total),
            'id' => $uuid,
        ];
    }

    public function extract(DOMDocument $document): string
    {
        return $this->format($this->obtain($document));
    }

    public function format(array $values): string
    {
        $receptorKey = 'rr';
        if (isset($values['rr'])) {
            $values['rr'] = $this->formatRfc($values[$receptorKey]);
        }
        if (isset($values['nr'])) {
            $receptorKey = 'nr';
            $values['nr'] = $this->formatForeignTaxId($values['nr']);
        }
        return '?'
            . implode('&', [
                're=' . $this->formatRfc($values['re'] ?? ''),
                $receptorKey . '=' . ($values[$receptorKey] ?? ''),
                'tt=' . $this->formatTotal($values['tt'] ?? ''),
                'id=' . ($values['id'] ?? ''),
            ]);
    }

    /** @return array{string, string} */
    private function obtainReceptorValues(DOMHelper $helper): array
    {
        $rfcReceptorKey = 'rr';
        $rfcReceptor = $helper->findAttribute(
            'retenciones:Retenciones',
            'retenciones:Receptor',
            'retenciones:Nacional',
            'RFCRecep'
        );

        if (null === $rfcReceptor) {
            $rfcReceptorKey = 'nr';
            $rfcReceptor = $helper->findAttribute(
                'retenciones:Retenciones',
                'retenciones:Receptor',
                'retenciones:Extranjero',
                'NumRegIdTrib'
            );
        }
        if (null === $rfcReceptor) {
            throw new AttributeNotFoundException('RET 1.0 receiver tax id cannot be found');
        }

        return [$rfcReceptorKey, $rfcReceptor];
    }
}
