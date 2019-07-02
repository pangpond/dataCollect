<?php
namespace nextgensoft\formatter;

class FormatData
{
    const DEFAULT_STUDENTCODE      = 0;
    const DEFAULT_STUDENTCLASSROOM = 0;
    const DEFAULT_DATE             = 'NULL';
    const DEFAULT_CITIZEN_ID       = 'NULL';
    const DEFAULT_REFER_ID         = '0';
    const DEFAULT_STATUS           = '1';
    const DEFAULT_BLOODGROUP       = 'NULL';
    const DEFAULT_CARD_ID          = 'NULL';
    const DEFAULT_EVERYTHING       = 'NULL';
    const DEFAULT_TITLE            = 'NULL';
    const DEFAULT_IMAGE            = 'NULL';
    const DEFAULT_MOBILE           = 'NULL';

    const CARD_STATUS_REQUEST = 20;

    private $_THAIMONTH = [
        '01' => ['full' => 'มกราคม', 'short' => 'ม.ค.', 'full_eng' => 'January', 'short_eng' => 'Jan'],
        '02' => ['full' => 'กุมภาพันธ์', 'short' => 'ก.พ.', 'full_eng' => 'February', 'short_eng' => 'Feb'],
        '03' => ['full' => 'มีนาคม', 'short' => 'มี.ค.', 'full_eng' => 'March', 'short_eng' => 'Mar'],
        '04' => ['full' => 'เมษายน', 'short' => 'เม.ย.', 'other' => 'เม.ษ.', 'full_eng' => 'April', 'short_eng' => 'Apr'],
        '05' => ['full' => 'พฤษภาคม', 'short' => 'พ.ค.', 'full_eng' => 'May', 'short_eng' => 'May'],
        '06' => ['full' => 'มิถุนายน', 'short' => 'มิ.ย.', 'full_eng' => 'June', 'short_eng' => 'Jun'],
        '07' => ['full' => 'กรกฎาคม', 'short' => 'ก.ค.', 'full_eng' => 'July', 'short_eng' => 'Jul'],
        '08' => ['full' => 'สิงหาคม', 'short' => 'ส.ค.', 'full_eng' => 'August', 'short_eng' => 'Aug'],
        '09' => ['full' => 'กันยายน', 'short' => 'ก.ย.', 'full_eng' => 'September', 'short_eng' => 'Sep'],
        '10' => ['full' => 'ตุลาคม', 'short' => 'ต.ค.', 'full_eng' => 'October', 'short_eng' => 'Oct'],
        '11' => ['full' => 'พฤศจิกายน', 'short' => 'พ.ย.', 'full_eng' => 'November', 'short_eng' => 'Nov'],
        '12' => ['full' => 'ธันวาคม', 'short' => 'ธ.ค.', 'full_eng' => 'December', 'short_eng' => 'Dec'],
    ];

    private $_BLOODGROUP = [
            'A' => ['เอ'],
            'B' => ['บี'],
            'AB' => ['เอบี'],
            'O' => ['โอ'],
    ];

    function formatStudentCode($studentCode)
    {
        $studentCode = preg_replace('/[^0-9]/', '', $studentCode);

        if (empty($studentCode)) {
            $studentCode = self::DEFAULT_STUDENTCODE;
        }

        return trim($studentCode);
    }

    function formatPersonPrefix($prefix)
    {
        $prefix = trim($prefix);

        foreach (['ด.ช.', 'เด็กชาย'] as $prefixWord) {
            if (strpos($prefix, $prefixWord) !== false) {
                return 'ด.ช.';
            }
        }

        foreach (['ด.ญ.', 'เด็กหญิง'] as $prefixWord) {
            if (strpos($prefix, $prefixWord) !== false) {
                return 'ด.ญ.';
            }
        }

        foreach (['น.ส.', 'นางสาว', 'นางสาบ', 'นาวสาบ', 'นาวสาว'] as $prefixWord) {
            if (strpos($prefix, $prefixWord) !== false) {
                return 'น.ส.';
            }
        }

        foreach (['นาย'] as $prefixWord) {
            if (strpos($prefix, $prefixWord) !== false) {
                return 'นาย';
            }
        }
    }

    function formatPersonName($name)
    {
        $name = preg_replace('/[a-z,0-9]/', '', $name);
        $name = trim($name);
        if (empty($name)) {
            $name = self::DEFAULT_EVERYTHING;
        }

        return $name;
    }
    function filterClass($fulltext)
    {
        $class = "";
        $fulltext      = preg_replace('/ /', '', $fulltext); //trim ก-ฮ
        $fulltext      = preg_replace('/\./', '', $fulltext); //trim ก-ฮ
        $arrayData = explode('/', $fulltext);
        if (isset($arrayData[0]) && !empty($arrayData[0])) {
            $arrayData[0] = trim($arrayData[0]);
            $class         = $arrayData[0];
        } else {
            $class = $fulltext;
        }
        return $class;
    }
    function formatStudentClass($studentClass)
    {
        $studentClass = trim($studentClass);
        $studentClass = $this->filterClass($studentClass);
        if (empty($studentClass)) {
            return self::DEFAULT_EVERYTHING;
        } else {


            foreach (['ปวช.1', 'ปวช. 1', 'ปวช1'] as $studentClassWord) {
            if (strpos($studentClass, $studentClassWord) !== false) {
                    return 'ปวช.1';
                }
            }

            foreach (['ปวช.2', 'ปวช. 2', 'ปวช2'] as $studentClassWord) {
                if (strpos($studentClass, $studentClassWord) !== false) {
                    return 'ปวช.2';
                }
            }

            foreach (['ปวช.3', 'ปวช. 3', 'ปวช3'] as $studentClassWord) {
                if (strpos($studentClass, $studentClassWord) !== false) {
                    return 'ปวช.3';
                }
            }


            if (in_array($studentClass, ['ม.1', 'ม. 1', 'ม1', '1'])) {
                return 'ม.1';
            } else if (in_array($studentClass, ['ม.2', 'ม. 2', 'ม2', '2'])) {
                return 'ม.2';
            } else if (in_array($studentClass, ['ม.3', 'ม. 3', 'ม3', '3'])) {
                return 'ม.3';
            } else if (in_array($studentClass, ['ม.4', 'ม. 4', 'ม4', '4'])) {
                return 'ม.4';
            } else if (in_array($studentClass, ['ม.5', 'ม. 5', 'ม5', '5'])) {
                return 'ม.5';
            } else if (in_array($studentClass, ['ม.6', 'ม. 6', 'ม6', '6'])) {
                return 'ม.6';
            } else {
                return self::DEFAULT_EVERYTHING;
            }

        }
    }

    function formatStudentClassroom($studentClassroom)
    {
        if (empty($studentClassroom)) {
            $studentClassroom = self::DEFAULT_STUDENTCLASSROOM;
        }

        return trim($studentClassroom);
    }

    function convertMonthTextToNumeric($monthText)
    {
        $date = preg_replace('/ /', '', $monthText); //trim space
        foreach ($this->_THAIMONTH as $key => $row) {
            foreach ($row as $value) {
                $date = preg_replace('/(' . $value . ')/', '-' . $key . '-', $date);
            }
        }

        $date = preg_replace('/[a-z]/', '', $date);
        $date = preg_replace('/[ก-ฮ\ ]/', '', $date);
        $date = preg_replace('/--/', '-', $date);
        $date = preg_replace('/\./', '', $date);//trim ก-ฮ

        return $date;
    }

    function formatDate($date)
    {
        $date = trim($date);
        $date = preg_replace('/--/', '-', $date);
        $date = preg_replace('/\//', '-', $date);
        if (empty($date)) {
            return self::DEFAULT_DATE;
        }
        $date = $this->convertMonthTextToNumeric($date);
        if (preg_match('/^([0-9]|[0-9]{2})-([0-9]|[0-9]{2})-[0-9]{4}$/', $date)) {
            //format DD-MM-YYYY
            $arrayDate    = explode('-', $date);
            $temp         = $arrayDate[0]; //swap
            $arrayDate[0] = $arrayDate[2];
            $arrayDate[2] = $temp;
            $date         = implode($arrayDate, '-'); //format YYYY-MM-DD
        }
        // format YYYY-MM-DD
        $arrayDate = explode('-', $date);
        if (empty($arrayDate[0]) || empty($arrayDate[1]) || empty($arrayDate[2])) {
            return self::DEFAULT_DATE;
        }

        $year  = $arrayDate[0];
        $month = $arrayDate[1];
        $day   = $arrayDate[2];

        if (isset($year) && (int) $year > 2100) {
            //convert Buddhist calendar
            $year = $year - 543;
        }
        if (isset($month) && (int) $month < 10) {
            //leading zero
            $month = '0' . (int) $month;
        }
        if (isset($day) && (int) $day < 10) {
            //leading zero
            $day = '0' . (int) $day;
        }
        $newDate = implode([$year, $month, $day], '-');
        if (!checkdate($month, $day, $year) || empty($newDate)) {
            //check real calendar
            $newDate = self::DEFAULT_DATE;
        }

        return $newDate;
    }
    function formatCitizenId($code)
    {
        $code = trim($code);
        if (empty($code)) {
            $code = self::DEFAULT_CITIZEN_ID;

            return $code;
        }
        $codeOnly = preg_replace('/-/', '', $code);
        if (!preg_match('/^[0-9]{13}$/', $codeOnly)) {
            $codeOnly = self::DEFAULT_CITIZEN_ID;

            return $codeOnly;
        }
        //format Digit
        for ($i = 0, $sum = 0; $i < 12; ++$i) {
            $sum += (int) ($codeOnly{$i}) * (13 - $i);
        }
        if ((11 - ($sum % 11)) % 10 != (int) ($codeOnly{12})) {
            $code = self::DEFAULT_CITIZEN_ID;

            return $code;
        }
        //format Citizen id 9-9999-99999-99-9
        if (preg_match('/^[0-9]-[0-9]{4}-[0-9]{5}-[0-9]{2}-[0-9]$/', $code)) {
            return $code;
        } elseif (preg_match('/^[0-9]{13}$/', preg_replace('/-/', '', $code))) {
            $code = preg_replace('/-/', '', $code);
            $code = substr_replace($code, '-', 1, 0);
            $code = substr_replace($code, '-', 6, 0);
            $code = substr_replace($code, '-', 12, 0);
            $code = substr_replace($code, '-', 15, 0);
        }

        return $code;
    }
    function formatMobileNumber($number)
    {
        $number = trim($number);
        $number = preg_replace('/[a-z]/', '', $number);
        $number = preg_replace('/ /', '', $number);
        if (strlen(preg_replace('/-/', '', $number)) == 9) {
            //check 9 digit
            $number = '0' . $number;
        }
        if (preg_match('/^[0-9]{10}$/', preg_replace('/-/', '', $number))) {
            //add format 999-999-9999
            $number = preg_replace('/-/', '', $number);
            $number = substr_replace($number, '0', 0, 1);
            $number = substr_replace($number, '-', 3, 0);
            $number = substr_replace($number, '-', 7, 0);
        } else {
            $number = self::DEFAULT_EVERYTHING;
        }
        if (empty($number)) {
            $number = self::DEFAULT_EVERYTHING;
        }

        return $number;
    }
    function formatPersonPrefixName($name)
    {
        $name       = trim($name);
        $name       = preg_replace('/ /', '', $name);
        $name       = preg_replace('/[a-z,0-9]/', '', $name);
        $prefixList = array(
            'เด็กชาย',
            'ด.ช.',
            'เด็กหญิง',
            'ด.ญ.',
            'นางสาว',
            'น.ส.',
            'นาย',
            'นางสาบ',
        );
        foreach ($prefixList as $key => $prefix) {
            $name = preg_replace('/^' . $prefix . '/', '', $name);
        }
        if (mb_substr($name, 0, 3) == 'นาง') {
            $tempName = mb_substr($name, 3);
            if ($tempName != '') {
                $name = mb_substr($name, 3);
            }
        }

        $name = preg_replace('/\./', '', $name);

        return $name;
    }
    function formatBloodGroup($bloodGroup)
    {
        $bloodGroup = trim($bloodGroup);
        $bloodGroup = preg_replace('/-/', '', $bloodGroup);
        $bloodGroup = preg_replace('/[0-9]/', '', $bloodGroup);
        foreach ($this->_BLOODGROUP as $key => $row) {
            foreach ($row as $value) {
                $bloodGroup = preg_replace('/('.$value.')/', $key, $bloodGroup);
            }
        }
        $bloodGroup = preg_replace('/[ก-ฮ\ ]/', '', $bloodGroup);//trim ก-ฮ
        $bloodGroup = strtoupper($bloodGroup);
        if (is_null($bloodGroup) || $bloodGroup === '') {
            $bloodGroup = self::DEFAULT_BLOODGROUP;
        }

        return $bloodGroup;
    }
    function formatCardId($cardId)
    {
        foreach (['แจ้งทำบัตร', 'แจ้งทำบัตรใหม่'] as $cardStatus) {
            if (strpos($cardId, $cardStatus) !== false) {
                return self::CARD_STATUS_REQUEST;
            }
        }

        $cardId = preg_replace('/[ก-ฮ\ ]/', '', $cardId); //trim ก-ฮ
        if (empty($cardId)) {
            $cardId = self::DEFAULT_EVERYTHING;
        } else {
            $cardId = str_pad($cardId, 10, '0', STR_PAD_LEFT);
        }

        return $cardId;
    }

    function formatFullClass($data)
    {
        $data = trim($data);
        if (in_array($data, [1, 2, 3, 4, 5, 6])) {
            $data = 'มัธยมศึกษาปีที่ '.$data;
        }
        $data = str_replace('ม.', 'มัธยมศึกษาปีที่ ', $data);
        $data = str_replace('ปวช.', 'ประกาศนียบัตรวิชาชีพปีที่ ', $data);
        $data = str_replace('  ', ' ', $data);

        $arrayData = explode('/', $data);
        if (isset($arrayData[0]) && !empty($arrayData[0])) {
            $arrayData[0] = trim($arrayData[0]);
            $data         = $arrayData[0];
        }
        $arrayData = explode(' ', $data);

        if (isset($arrayData[1]) && !is_null($arrayData[1])) {
            $arrayData[1] = trim($arrayData[1]);
            $arrayData[1] = (int) $arrayData[1];
            if (!is_numeric($arrayData[1])) {
                $data = self::DEFAULT_EVERYTHING;
            } elseif ($arrayData[1] <= 0) {
                $data = self::DEFAULT_EVERYTHING;
            } elseif ($arrayData[1] > 6) {
                $data = self::DEFAULT_EVERYTHING;
            }
        } else {
            $data = self::DEFAULT_EVERYTHING;
        }
        if (empty($data)) {
            $data = self::DEFAULT_EVERYTHING;
        }

        return $data;
    }
    function formatRoom($room)
    {
        $room      = trim($room);
        $room      = preg_replace('/[ก-ฮ\ ]/', '', $room); //trim ก-ฮ
        $room      = preg_replace('/ /', '', $room); //trim ก-ฮ
        $room      = preg_replace('/\./', '', $room); //trim ก-ฮ
        $arrayData = explode('/', $room);
        if (isset($arrayData[1]) && !empty($arrayData[1])) {
            $arrayData[1] = trim($arrayData[1]);
            $room         = $arrayData[1];
        } elseif (isset($arrayData[0]) && preg_match('/^([0-9]{2}|[0-9])$/', $arrayData[0])) {
            $room = $arrayData[0];
        } else {
            $room = self::DEFAULT_EVERYTHING;
        }
        if (empty($room)) {
            $room = self::DEFAULT_EVERYTHING;
        }

        return $room;
    }
    function convertTitle($data)
    {
        $data = $this->formatPersonPrefix($data);
        switch ($data) {
            case 'ด.ช.':
                return '1';
            case 'ด.ญ.':
                return '2';
            case 'นาย':
                return '3';
            case 'น.ส.':
                return '4';
            case 'นาง':
                return '5';
        }

        return self::DEFAULT_EVERYTHING;
    }
    function splitFirstnameFromFullname($fullname)
    {
        $fullname  = trim($fullname);
        $firstname = $fullname;
        $fullname = preg_replace('/[a-z,0-9]/', '', $fullname);
        $spacePos = strpos($fullname, ' ');
        if ($spacePos != false) {
            $firstname = substr($fullname, 0, $spacePos);
        }
        if (empty($firstname)) {
            $firstname = self::DEFAULT_EVERYTHING;
        }

        return $firstname;
    }
    function splitLastnameFromFullname($fullname)
    {
        $fullname = trim($fullname);
        $lastname = $fullname;
        $fullname = preg_replace('/[a-z,0-9]/', '', $fullname);
        $spacePos = strpos($fullname, ' ');
        if ($spacePos != false) {
            $lastname = substr($fullname, $spacePos + 1);
        }
        if (empty($lastname)) {
            $lastname = self::DEFAULT_EVERYTHING;
        }

        return $lastname;
    }
}
