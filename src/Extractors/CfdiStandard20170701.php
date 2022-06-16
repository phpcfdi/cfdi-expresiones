<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Extractors;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\ExpressionExtractorInterface;
use PhpCfdi\CfdiExpresiones\Internal\DOMHelper;
use PhpCfdi\CfdiExpresiones\Internal\MatchDetector;

/**
 * Especificación técnica del código de barras bidimensional a incorporar en la representación impresa.
 * Esta versión se utiliza desde CFDI 3.3 vigente a partir de 2017-07-01.
 */
abstract class CfdiStandard20170701 implements ExpressionExtractorInterface
{
    /** @var MatchDetector */
    private $matchDetector;

    /** @var string */
    private $unmatchedExceptionMessage;

    public function __construct(MatchDetector $matchDetector, string $unmatchedExceptionMessage)
    {
        $this->matchDetector = $matchDetector;
        $this->unmatchedExceptionMessage = $unmatchedExceptionMessage;
    }

    public function matches(DOMDocument $document): bool
    {
        return $this->matchDetector->matches($document);
    }

    public function obtain(DOMDocument $document): array
    {
        if (! $this->matches($document)) {
            throw new UnmatchedDocumentException($this->unmatchedExceptionMessage);
        }

        $helper = new DOMHelper($document);
        $uuid = $helper->getAttribute('cfdi:Comprobante', 'cfdi:Complemento', 'tfd:TimbreFiscalDigital', 'UUID');
        $rfcEmisor = $helper->getAttribute('cfdi:Comprobante', 'cfdi:Emisor', 'Rfc');
        $rfcReceptor = $helper->getAttribute('cfdi:Comprobante', 'cfdi:Receptor', 'Rfc');
        $total = $helper->getAttribute('cfdi:Comprobante', 'Total');
        $sello = $this->formatSello($helper->getAttribute('cfdi:Comprobante', 'Sello'));

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
                're=' . $this->formatRfc($values['re'] ?? ''),
                'rr=' . $this->formatRfc($values['rr'] ?? ''),
                'tt=' . $this->formatTotal($values['tt'] ?? ''),
                'fe=' . $this->formatSello($values['fe'] ?? ''),
            ]);
    }

    public function formatRfc(string $rfc): string
    {
        return htmlentities($rfc, ENT_XML1);
    }

    public function formatTotal(string $input): string
    {
        $total = rtrim(number_format(floatval($input), 6, '.', ''), '0');
        if ('.' === substr($total, -1)) {
            $total = $total . '0'; // add trailing zero
        }
        return $total;
    }

    public function formatSello(string $sello): string
    {
        return substr($sello, -8);
    }
}
