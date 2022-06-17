<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Extractors;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\Exceptions\AttributeNotFoundException;
use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\ExpressionExtractorInterface;
use PhpCfdi\CfdiExpresiones\Internal\DOMHelper;
use PhpCfdi\CfdiExpresiones\Internal\MatchDetector;

class Retenciones20 implements ExpressionExtractorInterface
{
    /** @var MatchDetector */
    private $matchDetector;

    public function __construct()
    {
        $this->matchDetector = new MatchDetector(
            'http://www.sat.gob.mx/esquemas/retencionpago/2',
            'retenciones:Retenciones',
            'Version',
            '2.0'
        );
    }

    public function uniqueName(): string
    {
        return 'RET20';
    }

    public function matches(DOMDocument $document): bool
    {
        return $this->matchDetector->matches($document);
    }

    public function obtain(DOMDocument $document): array
    {
        if (! $this->matches($document)) {
            throw new UnmatchedDocumentException('The document is not a RET 2.0');
        }

        $helper = new DOMHelper($document);

        $uuid = $helper->getAttribute(
            'retenciones:Retenciones',
            'retenciones:Complemento',
            'tfd:TimbreFiscalDigital',
            'UUID'
        );

        $rfcEmisor = $helper->getAttribute('retenciones:Retenciones', 'retenciones:Emisor', 'RfcE');

        $rfcReceptorKey = 'rr';
        $rfcReceptor = $helper->findAttribute(
            'retenciones:Retenciones',
            'retenciones:Receptor',
            'retenciones:Nacional',
            'RfcR'
        );

        if (null === $rfcReceptor) {
            $rfcReceptorKey = 'nr';
            $rfcReceptor = $helper->findAttribute(
                'retenciones:Retenciones',
                'retenciones:Receptor',
                'retenciones:Extranjero',
                'NumRegIdTribR'
            );
        }
        if (null === $rfcReceptor) {
            throw new AttributeNotFoundException('RET 2.0 receiver tax id cannot be found');
        }
        if ('nr' === $rfcReceptorKey) {
            $rfcReceptor = $this->formatForeignTaxId($rfcReceptor);
        }

        $total = $helper->getAttribute('retenciones:Retenciones', 'retenciones:Totales', 'MontoTotOperacion');

        $sello = (string) substr($helper->getAttribute('retenciones:Retenciones', 'Sello'), -8);

        return [
            'id' => $uuid,
            're' => $rfcEmisor,
            $rfcReceptorKey => $rfcReceptor,
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
        $receptorKey = 'rr';
        if (isset($values['rr'])) {
            $values[$receptorKey] = $this->formatRfc($values[$receptorKey]);
        }
        if (isset($values['nr'])) {
            $receptorKey = 'nr';
            $values['nr'] = $this->formatForeignTaxId($values['nr']);
        }
        return 'https://prodretencionverificacion.clouda.sat.gob.mx?'
            . implode('&', [
                'id=' . ($values['id'] ?? ''),
                're=' . $this->formatRfc($values['re'] ?? ''),
                $receptorKey . '=' . ($values[$receptorKey] ?? ''),
                'tt=' . $this->formatTotal($values['tt'] ?? ''),
                'fe=' . $this->formatSello($values['fe'] ?? ''),
            ]);
    }

    public function formatRfc(string $rfc): string
    {
        return htmlentities($rfc, ENT_XML1);
    }

    public function formatForeignTaxId(string $foreignTaxId): string
    {
        // codificar
        $foreignTaxId = htmlentities($foreignTaxId, ENT_XML1);
        // usar hasta un máximo de 20 posiciones
        $foreignTaxId = mb_substr($foreignTaxId, 0, 20);
        // crear un padding para establecer a 20 posiciones
        $padding = str_repeat('0', max(0, 20 - mb_strlen($foreignTaxId)));
        return $padding . $foreignTaxId;
    }

    public function formatTotal(string $input): string
    {
        $total = rtrim(number_format(floatval($input), 6, '.', ''), '0');
        if ('.' === substr($total, -1)) {
            $total = $total . '0'; // add trailing zero
        }
        return $total;
    }

    public function formatSello(string $input): string
    {
        return substr($input, -8);
    }
}
