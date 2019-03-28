<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\Extractors\Comprobante32;
use PhpCfdi\CfdiExpresiones\Extractors\Comprobante33;
use PhpCfdi\CfdiExpresiones\Extractors\Retenciones10;

class ExpressionExtractor implements ExpressionExtractorInterface
{
    /** @var ExpressionExtractorInterface[] */
    private $expressions;

    public function __construct(ExpressionExtractorInterface ...$expressions)
    {
        if ([] === $expressions) {
            $expressions = $this->defaultExtractors();
        }
        $this->expressions = $expressions;
    }

    /**
     * @return ExpressionExtractorInterface[];
     */
    public function defaultExtractors(): array
    {
        return [
            new Comprobante33(),
            new Comprobante32(),
            new Retenciones10(),
        ];
    }

    public function currentExpressionExtractors(): array
    {
        return $this->expressions;
    }

    protected function findMatch(DOMDocument $document): ?ExpressionExtractorInterface
    {
        foreach ($this->expressions as $expression) {
            if ($expression->matches($document)) {
                return $expression;
            }
        }
        return null;
    }

    protected function getFirstMatch(DOMDocument $document): ExpressionExtractorInterface
    {
        $discovered = $this->findMatch($document);
        if (null === $discovered) {
            throw new UnmatchedDocumentException('Cannot discover any ExpressionExtractor that matches with document');
        }
        return $discovered;
    }

    public function matches(DOMDocument $document): bool
    {
        return (null !== $this->findMatch($document));
    }

    public function extract(DOMDocument $document): string
    {
        $discovered = $this->getFirstMatch($document);
        return $discovered->extract($document);
    }
}
