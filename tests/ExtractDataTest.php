<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/formatter/ExtractData.php';
require __DIR__ . '/../src/formatter/FormatData.php';
require __DIR__ . '/../src/data/paragraph.php';
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
        $this->validationData = new ValidationData(1);
        $this->extractData = new ExtractData();
    }

    public function testExtractParagraph()
    {
        $paragraphArray = $this->extractData->extractParagraph(DEFAULT_PARAGRAPH);
        $this->assertCount(4, $paragraphArray);

        $schoolName = $paragraphArray[0];
        $this->assertGreaterThan(1, strpos($schoolName, 'สระบุรีวิทยาคม'));

        // $data['schoolName'] = array_search($schoolName, SCHOOL_SUGGEST);

        // $data['schoolName'] = array_search($schoolName, array_column(SCHOOL_SUGGEST));

        // var_dump($data['schoolName']);exit;

        // $data['schoolName'] = $this->extractData->findSchoolName($schoolName);
        // $this->assertEquals('สระบุรีวิทยาคม', $data['schoolName']);

        // $studentCode = $this->formatData->formatStudentCode('002874');
        // $this->assertTrue(is_numeric($studentCode));
        // $this->assertEquals(trim(DEFAULT_PARAGRAPH), $paragraph);

        // $studentCode = $this->formatData->formatStudentCode('07465eee');
        // $this->assertTrue(is_numeric($studentCode));
    }
    public function testExtractSchoolName()
    {
        $schoolName = '1.ชื่อโรงเรียน สระบุรีวิทยาคม';
        $data['schoolName'] = $this->extractData->findSchoolName($schoolName);
        $this->assertEquals('สระบุรีวิทยาคม', $data['schoolName']);

        $schoolName = '1.ชื่อโรงเรียน สบว';
        $data['schoolName'] = $this->extractData->findSchoolName($schoolName);
        $this->assertEquals('สระบุรีวิทยาคม', $data['schoolName']);

        $schoolName = '1.ชื่อโรงเรียน สระบุรี';
        $data['schoolName'] = $this->extractData->findSchoolName($schoolName);
        $this->assertEquals('สระบุรีวิทยาคม', $data['schoolName']);

        $schoolName = 'สระบุรี';
        $data['schoolName'] = $this->extractData->findSchoolName($schoolName);
        $this->assertEquals('สระบุรีวิทยาคม', $data['schoolName']);

        $schoolName = '1.ชื่อโรงเรียน การุ้ง';
        $data['schoolName'] = $this->extractData->findSchoolName($schoolName);
        $this->assertEquals('การุ้งวิทยาคม', $data['schoolName']);
    }

}
