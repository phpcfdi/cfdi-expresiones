<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\Exceptions\UnmatchedDocumentException;
use PhpCfdi\CfdiExpresiones\Extractors\Comprobante32;
use PhpCfdi\CfdiExpresiones\Extractors\Comprobante33;
use PhpCfdi\CfdiExpresiones\Extractors\Comprobante40;
use PhpCfdi\CfdiExpresiones\Extractors\Retenciones10;
use PhpCfdi\CfdiExpresiones\Extractors\Retenciones20;

class DiscoverExtractor implements ExpressionExtractorInterface
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

    /** @return ExpressionExtractorInterface[] */
    public function defaultExtractors(): array
    {
        return [
            new Comprobante40(),
            new Comprobante33(),
            new Comprobante32(),
            new Retenciones20(),
            new Retenciones10(),
        ];
    }

    /** @return ExpressionExtractorInterface[] */
    public function currentExpressionExtractors(): array
    {
        return $this->expressions;
    }

    protected function findByUniqueName(string $uniqueName): ?ExpressionExtractorInterface
    {
        foreach ($this->expressions as $expression) {
            if ($uniqueName === $expression->uniqueName()) {
                return $expression;
            }
        }
        return null;
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
            throw new UnmatchedDocumentException('Cannot discover any DiscoverExtractor that matches with document');
        }
        return $discovered;
    }

    public function matches(DOMDocument $document): bool
    {
        return (null !== $this->findMatch($document));
    }

    public function uniqueName(): string
    {
        return 'discover';
    }

    public function obtain(DOMDocument $document): array
    {
        $discovered = $this->getFirstMatch($document);
        return $discovered->obtain($document);
    }

    public function extract(DOMDocument $document): string
    {
        $discovered = $this->getFirstMatch($document);
        return $discovered->extract($document);
    }

    public function format(array $values, string $type = ''): string
    {
        $extractor = $this->findByUniqueName($type);
        if (null === $extractor) {
            throw new UnmatchedDocumentException('DiscoverExtractor requires type key with an extractor identifier');
        }
        unset($values['type']);
        return $extractor->format($values);
    }
}
