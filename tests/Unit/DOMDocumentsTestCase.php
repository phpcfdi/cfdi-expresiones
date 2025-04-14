<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiExpresiones\Tests\Unit;

use DOMDocument;
use PhpCfdi\CfdiExpresiones\Tests\TestCase;

abstract class DOMDocumentsTestCase extends TestCase
{
    public static function documentCfdi40(): DOMDocument
    {
        $document = new DOMDocument();
        $document->load(static::filePath('cfdi40-real.xml'));
        return $document;
    }

    public static function documentCfdi33(): DOMDocument
    {
        $document = new DOMDocument();
        $document->load(static::filePath('cfdi33-real.xml'));
        return $document;
    }

    public static function documentCfdi32(): DOMDocument
    {
        $document = new DOMDocument();
        $document->load(static::filePath('cfdi32-real.xml'));
        return $document;
    }

    public static function documentRet20Mexican(): DOMDocument
    {
        $document = new DOMDocument();
        $document->load(static::filePath('ret20-mexican-fake.xml'));
        return $document;
    }

    public static function documentRet20Foreign(): DOMDocument
    {
        $document = new DOMDocument();
        $document->load(static::filePath('ret20-foreign-fake.xml'));
        return $document;
    }

    public static function documentRet10Mexican(): DOMDocument
    {
        $document = new DOMDocument();
        $document->load(static::filePath('ret10-mexican-fake.xml'));
        return $document;
    }

    public static function documentRet10Foreign(): DOMDocument
    {
        $document = new DOMDocument();
        $document->load(static::filePath('ret10-foreign-fake.xml'));
        return $document;
    }
}
