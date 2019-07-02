<?php

class ValidationData
{
    const BLOODGROUP_A = 'A';
    const BLOODGROUP_B = 'B';
    const BLOODGROUP_AB = 'AB';
    const BLOODGROUP_O = 'O';
    const REGULAR_DATE_FORMAT = '/^(19|2[0-1])[0-9][0-9]-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/';
    const REGULAR_CITIZEN_FORMAT = '/^[0-9]-[0-9]{4}-[0-9]{5}-[0-9]{2}-[0-9]$/';
    const REGULAR_MOBILE_FORMAT = '/^0[0-9]{2}-[0-9]{3}-[0-9]{4}$/';
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    public function validateDate($date)
    {
        if (empty($date) || $date == 'NULL') {
           return true;
        }
        $arrayDate = explode('-', $date);
        if (!isset($arrayDate[0]) || !isset($arrayDate[1]) || !isset($arrayDate[2])) {
            return false;
        }
        $year = $arrayDate[0];
        $month = $arrayDate[1];
        $day = $arrayDate[2];
        $result = false;
        if (!checkdate($month,$day,$year)) {
            $result = true;
        }
        if (preg_match(self::REGULAR_DATE_FORMAT, $date)) {
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }
    public function validateDigitCitizenId($code)
    {
        if (empty($code)) {
            return true;
        }
        if (!preg_match(self::REGULAR_CITIZEN_FORMAT, $code)) {
            return false;
        }
        $code = preg_replace('/-/', '', $code);
        if (strlen($code) != 13) {
            return false;
        }
        for ($i = 0, $sum = 0; $i < 12;++$i) {
            $sum += (int) ($code{$i}) * (13 - $i);
        }
        if ((11 - ($sum % 11)) % 10 == (int) ($code{12})) {
            return true;
        }

        return false;
    }
    public function validateMobile($number)
    {
        if (empty($number)) {
            return true;
        }
        if (preg_match(self::REGULAR_MOBILE_FORMAT, $number)) {
            return true;
        }

        return false;
    }
    public function validateBloodGroup($bloodGroup)
    {
        switch ($bloodGroup) {
            case self::BLOODGROUP_A:
                return true;
            case self::BLOODGROUP_B:
                return true;
            case self::BLOODGROUP_AB:
                return true;
            case self::BLOODGROUP_O:
                return true;
        }
        return false;
    }
}
