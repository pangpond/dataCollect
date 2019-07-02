<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/formatter/FormatData.php';
require __DIR__ . '/ValidationData.php';

use nextgensoft\formatter\FormatData;
use PHPUnit\Framework\TestCase;

class FormatDataTest extends TestCase
{
    public $formatData;

    protected function setup(): void
    {
        $this->formatData = new FormatData(1);
        $this->validationData = new ValidationData(1);
    }

    public function testStudentCodeShouldBeNumericOnly()
    {
        $studentCode = $this->formatData->formatStudentCode(' 02874 ');
        $this->assertTrue(is_numeric($studentCode));

        $studentCode = $this->formatData->formatStudentCode('002874');
        $this->assertTrue(is_numeric($studentCode));

        $studentCode = $this->formatData->formatStudentCode('07465eee');
        $this->assertTrue(is_numeric($studentCode));
    }

    public function testWhenFoundOnlyNonNumericInStudentCodeItShouldReturnDefaultStudentCode()
    {
        $studentCode = $this->formatData->formatStudentCode('eee');
        $this->assertTrue(is_numeric($studentCode));

        $studentCode = $this->formatData->formatStudentCode('');
        $this->assertTrue(is_numeric($studentCode));
    }

    public function testPrefixMissMatchThatKnowShouldReturnCorrect()
    {
        $prefix = $this->formatData->formatPersonPrefix('เด็กชาย');
        $this->assertEquals('ด.ช.', $prefix);

        $prefix = $this->formatData->formatPersonPrefix('เด็กหญิง');
        $this->assertEquals('ด.ญ.', $prefix);

        $prefix = $this->formatData->formatPersonPrefix('ด.ช.');
        $this->assertEquals('ด.ช.', $prefix);

        $prefix = $this->formatData->formatPersonPrefix('ด.ญ.');
        $this->assertEquals('ด.ญ.', $prefix);

        $prefix = $this->formatData->formatPersonPrefix('นางสาว');
        $this->assertEquals('น.ส.', $prefix);

        $prefix = $this->formatData->formatPersonPrefix('น.ส.');
        $this->assertEquals('น.ส.', $prefix);

        $prefix = $this->formatData->formatPersonPrefix('นาย');
        $this->assertEquals('นาย', $prefix);

        $prefix = $this->formatData->formatPersonPrefix(' ด.ช. ');
        $this->assertEquals('ด.ช.', $prefix);

        // $prefix = $this->formatData->formatPersonPrefix('*ด.ช.wee');
        // $this->assertEquals('ด.ช.', $prefix);

        // $prefix = $this->formatData->formatPersonPrefix('*ด.ญ.wee');
        // $this->assertEquals('ด.ญ.', $prefix);

        $prefix = $this->formatData->formatPersonPrefix('นางสาบ');
        $this->assertEquals('น.ส.', $prefix);

        $prefix = $this->formatData->formatPersonPrefix('นาวสาบ');
        $this->assertEquals('น.ส.', $prefix);
    }

    public function testNameShouldBeStringOnly()
    {
        $name = $this->formatData->formatPersonName(' จารุวรรณ ');
        $this->assertTrue(!is_numeric($name));
        $this->assertEquals('จารุวรรณ', $name);

        $name = $this->formatData->formatPersonName('1aจารุว2รรณs');
        $this->assertTrue(!is_numeric($name));
        $this->assertEquals('จารุวรรณ', $name);
    }

    public function testStudentClassShouldInAcceptList()
    {
        foreach (['1', '2', '3', '4', '5', '6'] as $studentClassNo) {
            $studentClass = $this->formatData->formatStudentClass('ม.' . $studentClassNo);
            $this->assertEquals('ม.' . $studentClassNo, $studentClass);

            $studentClass = $this->formatData->formatStudentClass('ม. ' . $studentClassNo);
            $this->assertEquals('ม.' . $studentClassNo, $studentClass);

            $studentClass = $this->formatData->formatStudentClass('ม' . $studentClassNo);
            $this->assertEquals('ม.' . $studentClassNo, $studentClass);
        }

        $studentClass = $this->formatData->formatStudentClass('1');
        $this->assertEquals('ม.1', $studentClass);

        $studentClass = $this->formatData->formatStudentClass('7');
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $studentClass);

        $studentClass = $this->formatData->formatStudentClass('ม.7');
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $studentClass);

        $studentClass = $this->formatData->formatStudentClass('ปวช.1');
        $this->assertEquals('ปวช.1', $studentClass);

        $studentClass = $this->formatData->formatStudentClass('ปวช.2');
        $this->assertEquals('ปวช.2', $studentClass);

        $studentClass = $this->formatData->formatStudentClass('ปวช.3');
        $this->assertEquals('ปวช.3', $studentClass);

        $studentClass = $this->formatData->formatStudentClass('ม.3/2');
        $this->assertEquals('ม.3', $studentClass);

    }

    public function testStudentClassroomShouldInAcceptList()
    {
        $name = $this->formatData->formatStudentClassroom('1');
        $this->assertTrue(is_numeric($name));
    }

    public function testStudentClassroomShouldReturnDefaultWhenNull()
    {
        $name = $this->formatData->formatStudentClassroom('');
        $this->assertTrue(is_numeric($name));
    }

    public function testDateShouldSqlFormatOnly()
    {
        // //between 1900-01-01 and 2100-12-31
        $date = $this->formatData->formatDate('2016-02-02');
        $this->assertTrue($this->validationData->validateDate($date));
        $this->assertEquals('2016-02-02', $date);

        $date = $this->formatData->formatDate('2559-02-02');
        $this->assertTrue($this->validationData->validateDate($date));
        $this->assertEquals('2016-02-02', $date);

        $date = $this->formatData->formatDate('2559-2-2');
        $this->assertTrue($this->validationData->validateDate($date));
        $this->assertEquals('2016-02-02', $date);

        $date = $this->formatData->formatDate('  2559-02-02 ');
        $this->assertTrue($this->validationData->validateDate($date));
        $this->assertEquals('2016-02-02', $date);

        $date = $this->formatData->formatDate('2000--01-22');
        $this->assertTrue($this->validationData->validateDate($date));
        $this->assertEquals('2000-01-22', $date);

        $date = $this->formatData->formatDate('2559-02-31');
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $date);

        $date = $this->formatData->formatDate('25566-03-00');
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $date);

        $date = $this->formatData->formatDate('2553-1-dd');
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $date);

        $date = $this->formatData->formatDate('0000-00-00');
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $date);

        $date = $this->formatData->formatDate('29/02/2543');
        $this->assertTrue($this->validationData->validateDate($date));
        $this->assertEquals('2000-02-29', $date);

        $date = $this->formatData->formatDate('15 กันยายน 2546');
        $this->assertTrue($this->validationData->validateDate($date));
        $this->assertEquals('2003-09-15', $date);

        $date = $this->formatData->formatDate('15 ก.ย. 2546');
        $this->assertTrue($this->validationData->validateDate($date));
        $this->assertEquals('2003-09-15', $date);

        $date = $this->formatData->formatDate('15กันยายน2546');
        $this->assertTrue($this->validationData->validateDate($date));
        $this->assertEquals('2003-09-15', $date);

        $date = $this->formatData->formatDate('15Jan2546');
        $this->assertTrue($this->validationData->validateDate($date));
        $this->assertEquals('2003-01-15', $date);

        $date = $this->formatData->formatDate('15-Jan-2546');
        $this->assertTrue($this->validationData->validateDate($date));
        $this->assertEquals('2003-01-15', $date);

        $date = $this->formatData->formatDate('15/Jan/2546');
        $this->assertTrue($this->validationData->validateDate($date));
        $this->assertEquals('2003-01-15', $date);

        $date = $this->formatData->formatDate('');
        $this->assertTrue($this->validationData->validateDate($date));
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $date);
    }

    public function testDateShouldReturnDefaultWhenNull()
    {
        $date = $this->formatData->formatDate('');
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $date);
    }

    public function testCitizenIdShouldReturnDefaultCitizenId()
    {
        //check digit
        $code = $this->formatData->formatCitizenId('1-1006-97754-32-1');
        $this->assertTrue($this->validationData->validateDigitCitizenId($code));
        $this->assertEquals('1-1006-97754-32-1', $code);

        $code = $this->formatData->formatCitizenId('1100697754321');
        $this->assertTrue($this->validationData->validateDigitCitizenId($code));
        $this->assertEquals('1-1006-97754-32-1', $code);

        $code = $this->formatData->formatCitizenId('1-10-06---97754-32-1');
        $this->assertTrue($this->validationData->validateDigitCitizenId($code));
        $this->assertEquals('1-1006-97754-32-1', $code);

        $code = $this->formatData->formatCitizenId('1-1006-97754-32-22'); //digit invalid
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $code);
    }

    public function testCitizenIdShouldReturnDefaultWhenNull()
    {
        $code = $this->formatData->formatCitizenId('');
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $code);
    }

    public function testMobileNumberShouldReturnMobileFormat()
    {
        $number = $this->formatData->formatMobileNumber('084-222-2222');
        $this->assertTrue($this->validationData->validateMobile($number));

        $number = $this->formatData->formatMobileNumber('842222222');
        $this->assertTrue($this->validationData->validateMobile($number));

        $number = $this->formatData->formatMobileNumber(' 08422ss22d222 ');
        $this->assertTrue($this->validationData->validateMobile($number));

        $number = $this->formatData->formatMobileNumber('084 222 2222');
        $this->assertTrue($this->validationData->validateMobile($number));
        $this->assertEquals('084-222-2222', $number);

        $number = $this->formatData->formatMobileNumber('281-723-2179');
        $this->assertTrue($this->validationData->validateMobile($number));
        $this->assertEquals('081-723-2179', $number);

        $number = $this->formatData->formatMobileNumber('08775666075');
        $this->assertEquals('NULL', $number);

    }

    public function testMobileNumberShouldReturnDefaultWhenNull()
    {
        $number = $this->formatData->formatMobileNumber('');
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $number);

        $number = $this->formatData->formatMobileNumber(null);
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $number);

        $number = $this->formatData->formatMobileNumber('NULL');
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $number);
    }

    public function testFirstNameShouldNotPersonPrefix()
    {
        $name = $this->formatData->formatPersonPrefixName('เด็กชายจารุวรรณ');
        $this->assertTrue(!is_numeric($name));
        $this->assertEquals('จารุวรรณ', $name);

        $name = $this->formatData->formatPersonPrefixName('ด.ช.จารุวรรณ');
        $this->assertTrue(!is_numeric($name));
        $this->assertEquals('จารุวรรณ', $name);

        $name = $this->formatData->formatPersonPrefixName('ด.ช. จารุวรรณ');
        $this->assertTrue(!is_numeric($name));
        $this->assertEquals('จารุวรรณ', $name);

        $name = $this->formatData->formatPersonPrefixName(' ด.ช. จารุวรรณ');
        $this->assertTrue(!is_numeric($name));
        $this->assertEquals('จารุวรรณ', $name);

        $name = $this->formatData->formatPersonPrefixName('เด็กหญิงจารุวรรณ');
        $this->assertTrue(!is_numeric($name));
        $this->assertEquals('จารุวรรณ', $name);

        $name = $this->formatData->formatPersonPrefixName('ด.ญ.จารุวรรณ');
        $this->assertTrue(!is_numeric($name));
        $this->assertEquals('จารุวรรณ', $name);

        $name = $this->formatData->formatPersonPrefixName('นายจารุวรรณ');
        $this->assertTrue(!is_numeric($name));
        $this->assertEquals('จารุวรรณ', $name);

        $name = $this->formatData->formatPersonPrefixName('นางสาวจารุวรรณ');
        $this->assertTrue(!is_numeric($name));
        $this->assertEquals('จารุวรรณ', $name);

        $name = $this->formatData->formatPersonPrefixName('น.ส.จารุวรรณ');
        $this->assertTrue(!is_numeric($name));
        $this->assertEquals('จารุวรรณ', $name);

        $name = $this->formatData->formatPersonPrefixName('นางสาบจารุวรรณ');
        $this->assertTrue(!is_numeric($name));
        $this->assertEquals('จารุวรรณ', $name);

        $name = $this->formatData->formatPersonPrefixName('1aจารุว2รรณs');
        $this->assertTrue(!is_numeric($name));
        $this->assertEquals('จารุวรรณ', $name);
    }

    public function testBloodGroupShouldInAcceptList()
    {
        $bloodGroup = $this->formatData->formatBloodGroup('A');
        $this->assertTrue(!is_numeric($bloodGroup));
        $this->assertEquals('A', $bloodGroup);

        $bloodGroup = $this->formatData->formatBloodGroup('B');
        $this->assertTrue(!is_numeric($bloodGroup));
        $this->assertEquals('B', $bloodGroup);

        $bloodGroup = $this->formatData->formatBloodGroup('AB');
        $this->assertTrue(!is_numeric($bloodGroup));
        $this->assertEquals('AB', $bloodGroup);

        $bloodGroup = $this->formatData->formatBloodGroup('O');
        $this->assertTrue(!is_numeric($bloodGroup));
        $this->assertEquals('O', $bloodGroup);

        $bloodGroup = $this->formatData->formatBloodGroup(' a ');
        $this->assertTrue(!is_numeric($bloodGroup));
        $this->assertEquals('A', $bloodGroup);

        $bloodGroup = $this->formatData->formatBloodGroup('0');
        $this->assertTrue(!is_numeric($bloodGroup));
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $bloodGroup);

        $bloodGroup = $this->formatData->formatBloodGroup('-');
        $this->assertTrue(!is_numeric($bloodGroup));
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $bloodGroup);

        $bloodGroup = $this->formatData->formatBloodGroup('เอ');
        $this->assertTrue(!is_numeric($bloodGroup));
        $this->assertEquals('A', $bloodGroup);

        $bloodGroup = $this->formatData->formatBloodGroup('บี');
        $this->assertTrue(!is_numeric($bloodGroup));
        $this->assertEquals('B', $bloodGroup);

        $bloodGroup = $this->formatData->formatBloodGroup('เอบี');
        $this->assertTrue(!is_numeric($bloodGroup));
        $this->assertEquals('AB', $bloodGroup);

        $bloodGroup = $this->formatData->formatBloodGroup('โอ  ฟ3หกด');
        $this->assertTrue(!is_numeric($bloodGroup));
        $this->assertEquals('O', $bloodGroup);
    }

    public function testFullClassShouldInAcceptList()
    {
        $fullClass = $this->formatData->formatFullClass('1');
        $this->assertEquals('มัธยมศึกษาปีที่ 1', $fullClass);

        $fullClass = $this->formatData->formatFullClass('ม.1');
        $this->assertEquals('มัธยมศึกษาปีที่ 1', $fullClass);

        $fullClass = $this->formatData->formatFullClass('ม.1/1');
        $this->assertEquals('มัธยมศึกษาปีที่ 1', $fullClass);

        $fullClass = $this->formatData->formatFullClass('  ม. 1 / 1  ');
        $this->assertEquals('มัธยมศึกษาปีที่ 1', $fullClass);

        $fullClass = $this->formatData->formatFullClass('1');
        $this->assertEquals('มัธยมศึกษาปีที่ 1', $fullClass);

        $fullClass = $this->formatData->formatFullClass('มัธยมศึกษาปีที่ 0');
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $fullClass);

        $fullClass = $this->formatData->formatFullClass('มัธยมศึกษาปีที่');
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $fullClass);

        $fullClass = $this->formatData->formatFullClass('มัธยมศึกษาปีที่ 7');
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $fullClass);

        $fullClass = $this->formatData->formatFullClass('ประกาศนียบัตรวิชาชีพปีที่ 1');
        $this->assertEquals('ประกาศนียบัตรวิชาชีพปีที่ 1', $fullClass);

        $fullClass = $this->formatData->formatFullClass('ปวช. 1');
        $this->assertEquals('ประกาศนียบัตรวิชาชีพปีที่ 1', $fullClass);
    }

    public function testRoomShouldInAcceptList()
    {
        $fullClass = $this->formatData->formatRoom('1');
        $this->assertEquals('1', $fullClass);

        $fullClass = $this->formatData->formatRoom('ม.1');
        $this->assertEquals('1', $fullClass);

        $fullClass = $this->formatData->formatRoom('ม.1/2');
        $this->assertEquals('2', $fullClass);

        $fullClass = $this->formatData->formatRoom('  ม. 1 / 2  ');
        $this->assertEquals('2', $fullClass);
    }

    public function testSplitNameShouldReturnFirstname()
    {
        $fullname = $this->formatData->splitFirstnameFromFullname('นางสมร กล้าหาญ');
        $fullname = $this->formatData->formatPersonPrefixName($fullname);
        $this->assertEquals('สมร', $fullname);

        $fullname = $this->formatData->splitFirstnameFromFullname('นางธัญนัทธีร์');
        $fullname = $this->formatData->formatPersonPrefixName($fullname);
        $this->assertEquals('ธัญนัทธีร์', $fullname);

        $fullname = $this->formatData->splitFirstnameFromFullname('นางสมร');
        $fullname = $this->formatData->formatPersonPrefixName($fullname);
        $this->assertEquals('สมร', $fullname);

        $fullname = $this->formatData->splitFirstnameFromFullname('');
        $fullname = $this->formatData->formatPersonPrefixName($fullname);
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $fullname);

        $fullname = $this->formatData->splitFirstnameFromFullname('นาง');
        $fullname = $this->formatData->formatPersonPrefixName($fullname);
        $this->assertEquals('นาง', $fullname);

        $fullname = $this->formatData->splitFirstnameFromFullname('นางนาง');
        $fullname = $this->formatData->formatPersonPrefixName($fullname);
        $this->assertEquals('นาง', $fullname);
    }

    public function testSplitNameShouldReturnLastname()
    {
        $fullname = $this->formatData->splitLastnameFromFullname('บีนิตา สตีเฟตสัน');
        $this->assertEquals('สตีเฟตสัน', $fullname);

        $fullname = $this->formatData->splitLastnameFromFullname('นางสมร กล้าหาญ');
        $this->assertEquals('กล้าหาญ', $fullname);

        $fullname = $this->formatData->splitLastnameFromFullname('กล้าหาญ');
        $this->assertEquals('กล้าหาญ', $fullname);

        $fullname = $this->formatData->splitLastnameFromFullname(' ');
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $fullname);

        $fullname = $this->formatData->splitLastnameFromFullname('');
        $this->assertEquals($this->formatData::DEFAULT_EVERYTHING, $fullname);

        $fullname = $this->formatData->splitLastnameFromFullname('นางสมร ณ อยุธยา');
        $this->assertEquals('ณ อยุธยา', $fullname);

        $fullname = $this->formatData->splitLastnameFromFullname('นางสมร อิศรางกูร ณ อยุธยา');
        $this->assertEquals('อิศรางกูร ณ อยุธยา', $fullname);

        $fullname = $this->formatData->splitLastnameFromFullname('นายสุรเชษฐ ถาวร');
        $this->assertEquals('ถาวร', $fullname);
    }

    public function testReturnCardStatusWhenCardNoIsNotNumeric()
    {
        $cardNo = $this->formatData->formatCardId('แจ้งทำบัตรใหม่');
        $this->assertEquals($this->formatData::CARD_STATUS_REQUEST, $cardNo);

        $cardNo = $this->formatData->formatCardId('1234');
        $this->assertEquals('0000001234', $cardNo);
    }
}
