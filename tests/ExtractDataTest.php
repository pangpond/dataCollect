<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/formatter/ExtractData.php';
require __DIR__ . '/../src/formatter/FormatData.php';
require __DIR__ . '/ValidationData.php';

use nextgensoft\formatter\ExtractData;
use nextgensoft\formatter\FormatData;
use PHPUnit\Framework\TestCase;

class ExtractDataTest extends TestCase
{
    public $formatData;
    // public $extractData;

    protected function setup(): void
    {
        $this->formatData = new FormatData(1);
        // $this->validationData = new ValidationData(1);
        $this->extractData = new ExtractData();
    }

    public function testStudentCodeShouldBeNumericOnly()
    {
        $studentCode = $this->formatData->formatStudentCode(' 02874 ');
        $this->assertTrue(is_numeric($studentCode));

        // $studentCode = $this->formatData->formatStudentCode('002874');
        // $this->assertTrue(is_numeric($studentCode));

        // $studentCode = $this->formatData->formatStudentCode('07465eee');
        // $this->assertTrue(is_numeric($studentCode));
    }
}
