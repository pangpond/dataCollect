<?php

require __DIR__.'/formatter/FormatData.php';

use Nextgensoft\formatter\FormatData;

class FileService extends FormatData
{
    const HEADER_PATTERN_LIST = array(
        ['name' => 'student_code', 'require' => true, 'default' => self::DEFAULT_STUDENTCODE],
        ['name' => 'refer_id', 'require' => false, 'default' => self::DEFAULT_REFER_ID],
        ['name' => 'firstname', 'require' => true, 'default' => self::DEFAULT_EVERYTHING],
        ['name' => 'lastname', 'require' => true, 'default' => self::DEFAULT_EVERYTHING],
        ['name' => 'citizenid', 'require' => true, 'duplicate' => false, 'default' => self::DEFAULT_CITIZEN_ID],
        ['name' => 'bloodgroup', 'require' => false, 'default' => self::DEFAULT_BLOODGROUP],
        ['name' => 'dob', 'require' => false, 'default' => self::DEFAULT_DATE],
        ['name' => 'class_full', 'require' => true, 'default' => self::DEFAULT_EVERYTHING],
        ['name' => 'class', 'require' => true, 'default' => self::DEFAULT_EVERYTHING],
        ['name' => 'room', 'require' => true, 'default' => self::DEFAULT_EVERYTHING],
        ['name' => 'card_id', 'require' => false, 'duplicate' => false, 'default' => self::DEFAULT_CARD_ID],
        ['name' => 'title', 'require' => false, 'default' => self::DEFAULT_TITLE],
        ['name' => 'school_name', 'require' => true, 'default' => self::DEFAULT_EVERYTHING],
        ['name' => 'address', 'require' => false, 'default' => self::DEFAULT_EVERYTHING],
        ['name' => 'sub_districts', 'require' => false, 'default' => self::DEFAULT_EVERYTHING],
        ['name' => 'districts', 'require' => false, 'default' => self::DEFAULT_EVERYTHING],
        ['name' => 'provinces', 'require' => false, 'default' => self::DEFAULT_EVERYTHING],
        ['name' => 'zipcode', 'require' => false, 'default' => self::DEFAULT_EVERYTHING],
        ['name' => 'image_name', 'require' => false, 'default' => self::DEFAULT_IMAGE],
        ['name' => 'parent_name', 'require' => false, 'default' => self::DEFAULT_EVERYTHING],
        ['name' => 'parent_lastname', 'require' => false, 'default' => self::DEFAULT_EVERYTHING],
        ['name' => 'mobile', 'require' => false, 'default' => self::DEFAULT_MOBILE],
        ['name' => 'status', 'require' => false, 'default' => self::DEFAULT_STATUS],
    );
    const NOT_DUPLICATE = array(
        ['student_code'],
        ['firstname', 'lastname'],
        ['citizenid'],
    );

    private $data;
    private $headerDataList = array();
    public function __construct()
    {
        $this->resetHeaderInputList();
    }
    public function resetHeaderInputList()
    {
        $count = count(self::HEADER_PATTERN_LIST);
        $this->headerDataList = array();
        for ($i = 0; $i < $count; ++$i) {
            array_push($this->headerDataList, null);
        }
    }
    public function getDefault($header)
    {
        foreach (self::HEADER_PATTERN_LIST as $key => $value) {
            if ($value['name'] == $header) {
                return $value['default'];
            }
        }

        return self::DEFAULT_EVERYTHING;
    }
    public function getHeaderKey($name)
    {
        foreach (self::HEADER_PATTERN_LIST as $key => $value) {
            if ($value['name'] == $name) {
                return $key;
            }
        }

        return;
    }
    public function setDataHeader($headerList)
    {
        $this->resetHeaderInputList();
        $invalid = array();
        foreach (self::HEADER_PATTERN_LIST as $patternKey => $patternHeader) {
            $notFound = true;
            if ($patternHeader['name'] == 'class_full') {
                $patternHeader['name'] = 'class';
            } //if class full watch class
            foreach ($headerList as $dataKey => $dataHeader) {
                if (strtolower($dataHeader) == strtolower($patternHeader['name'])) {
                    $this->headerDataList[$patternKey] = $dataKey;
                    $notFound = false;
                }
            }
            if ($notFound == true) {
                array_push($invalid, $patternHeader['name']);
            }
        }
        $text = implode($invalid, ',').' is not found or invalids';

        return $text;
    }

    public function setData($fileName)
    {
        $this->data = array();
        if (!file_exists($fileName)) {
            return false;
        }
        $file = fopen($fileName, 'r');
        $headerList = fgetcsv($file);
        $error = $this->setDataHeader($headerList);
        while ($row = fgetcsv($file)) {
            array_push($this->data, $row);
        }

        return ['header' => $headerList, 'error' => $error];
    }
    public function getRawData($data)
    {
        return $this->data;
    }
    public function processData($value, $header)
    {
        $tempValue = $value;
        switch ($header) {
            case 'student_id':
                $tempValue = $this->formatStudentCode($value);
            case 'refer_id':
                if (is_null($value) || $value == '') {
                    $tempValue = self::DEFAULT_REFER_ID;
                }
                break;
            case 'firstname':
                $tempValue = $this->formatPersonPrefixName($value);
                $tempValue = $this->formatPersonName($tempValue);
                break;
            case 'lastname':
                $tempValue = $this->formatPersonName($value);
                break;
            case 'citizenid':
                $tempValue = $this->formatCitizenId($value);
                break;
            case 'bloodgroup':
                $tempValue = $this->formatBloodGroup($value);
                break;
            case 'dob':
                $tempValue = $this->formatDate($value);
                break;
            case 'title':
                $tempValue = $this->convertTitle($value);
                break;
            case 'card_id':
                $tempValue = $this->formatCardId($value);
                break;
            case 'class_full':
                $tempValue = $this->formatFullClass($value);
                break;
            case 'class':
                $tempValue = $this->formatStudentClass($value);
                break;
            case 'room':
                $tempValue = $this->formatRoom($value);
                break;
            case 'parent_name':
                $tempValue = $this->splitFirstnameFromFullname($value);
                $tempValue = $this->formatPersonPrefixName($tempValue);
                $tempValue = $this->formatPersonName($tempValue);
                break;
            case 'parent_lastname':
                $tempValue = $this->splitLastnameFromFullname($value);
                $tempValue = $this->formatPersonPrefixName($tempValue);
                $tempValue = $this->formatPersonName($tempValue);
                break;
            case 'status':
                if (is_null($value) || $value == '') {
                    $tempValue = self::DEFAULT_STATUS;
                }
                break;
            case 'mobile':
                $tempValue = $this->formatMobileNumber($value);
                break;
            default:
                if (is_null($value) || $value == '') {
                    $tempValue = self::DEFAULT_EVERYTHING;
                }
                break;
        }

        return $tempValue;
    }

    public function getData()
    {
        $tempData = array();
        foreach ($this->data as $key => $row) {
            $tempRow = array();
            foreach ($this->headerDataList as $patternKey => $dataKey) {
                if (!is_null($dataKey) && isset($row[$dataKey])) {
                    $tempValue = $this->processData($row[$dataKey], self::HEADER_PATTERN_LIST[$patternKey]['name']);
                    array_push($tempRow, $tempValue);
                } else {
                    $tempValue = $this->processData(null, self::HEADER_PATTERN_LIST[$patternKey]['name']);
                    array_push($tempRow, $tempValue);
                }
            }
            $resultCheckRequire = $this->checkRequire($tempRow);
            $resultCheckDuplicate = $this->checkDuplicate($tempData, $tempRow);
            if ($resultCheckRequire['status'] === true && $resultCheckDuplicate['status'] === true) {
                array_push($tempData, $tempRow);
            }
        }

        return $tempData;
    }
    public function getFailData()
    {
        $tempData = array();
        $goodData = array();
        foreach ($this->data as $key => $row) {
            $tempRow = array();
            foreach ($this->headerDataList as $patternKey => $dataKey) {
                if (!is_null($dataKey) && isset($row[$dataKey])) {
                    $tempValue = $this->processData($row[$dataKey], self::HEADER_PATTERN_LIST[$patternKey]['name']);
                    array_push($tempRow, $tempValue);
                } else {
                    $tempValue = $this->processData(null, self::HEADER_PATTERN_LIST[$patternKey]['name']);
                    array_push($tempRow, $tempValue);
                }
            }
            $resultCheckRequire = $this->checkRequire($tempRow);
            $resultCheckDuplicate = $this->checkDuplicate($goodData, $tempRow);
            if ($resultCheckRequire['status'] === false) {
                array_push($tempRow, $resultCheckRequire['message']);
            }
            if ($resultCheckDuplicate['status'] === false) {
                array_push($tempRow, $resultCheckDuplicate['message']);
            }
            if ($resultCheckRequire['status'] === true && $resultCheckDuplicate['status'] === true) {
                array_push($goodData, $tempRow);
            } else {
                array_push($tempData, $tempRow);
            }
        }

        return $tempData;
    }
    public function checkRequire($checkedRow)
    {
        $message = array();
        $status = true;

        foreach (self::HEADER_PATTERN_LIST as $headerKey => $header) {
            //Loop Column
            if ($header['require'] == true) {
                $default = $this->getDefault($header['name']);
                if (empty($checkedRow[$headerKey]) || $checkedRow[$headerKey] == $default) {
                    array_push($message, $header['name']);
                    $status = false;
                }
            }
        }
        if (empty($message)) {
            $message = '';
        } else {
            $message = implode(',', $message).' is empty';
        }

        return ['status' => $status, 'message' => $message];
    }
    public function checkDuplicate($data, $checkedRow)
    {
        $message = array();
        $status = true;

        foreach ($data as $dataKey => $dataRow) {
            foreach (self::NOT_DUPLICATE as $keyNameList) {
                $isMatch = true;
                foreach ($keyNameList as $keyName) {
                    $default = $this->getDefault($keyName);
                    $headerKey = $this->getHeaderKey($keyName);
                    if (empty($checkedRow[$headerKey]) || $checkedRow[$headerKey] == $default) {
                        $isMatch = false;
                        break;
                    } elseif ($checkedRow[$headerKey] != $dataRow[$headerKey]) {
                        $isMatch = false;
                    }
                }
                if ($isMatch == true) {
                    $status = false;
                    foreach ($keyNameList as $keyName) {
                        array_push($message, $keyName);
                    }
                }
            }
        }
        if (empty($message)) {
            $message = '';
        } else {
            $message = implode(',', $message).' is duplicate';
        }

        return ['status' => $status, 'message' => $message];
    }
    public function getHeaderPattern()
    {
        $header = array_column(self::HEADER_PATTERN_LIST, 'name');

        return $header;
    }
    public function exportData($file)
    {
        $tempData = $this->getData();
        $export = fopen($file, 'w');
        foreach ($tempData as $key => $row) {
            fputcsv($export, $row);
        }
        fclose($export);

        return true;
    }
    public function exportFailData($file)
    {
        $tempData = $this->getFailData();
        $export = fopen($file, 'w');
        foreach ($tempData as $key => $row) {
            fputcsv($export, $row);
        }
        fclose($export);

        return true;
    }
}
