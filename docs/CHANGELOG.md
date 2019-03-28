# CHANGELOG

## Version 2.0.0 2019-03-28

- Allows to create an expression with format fixes from specific types
- Change `ExpressionExtractorInterface` to add `public function format(array $values): string`
- Give a unique name for extractors, so when discovering by type can obtain an item
- Change `ExpressionExtractorInterface` to add `public function uniqueName(): string`
    - `Comprobante33` uses `CFDI33`
    - `Comprobante32` uses `CFDI32`
    - `Retenciones10` uses `RET10`
- Rename `ExpressionExtractor` to `DiscoverExtractor`, it makes more sense


## Version 1.0.0 2019-03-27

- Create this package as is a common use between other packages
- Include Retenciones e información de pagos (RET10)
- Implement more tests
