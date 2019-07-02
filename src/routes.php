<?php
// Routes
const UPLOAD_PATH = '../public/fileupload/';
const EXPORT_PATH = '../public/fileexport/';
const FAIL_PATH = '../public/filefail/';
require __DIR__.'/FileService.php';

$app->get('/uploads', function ($request, $response) {
    $fileList = array();
    if (is_dir(UPLOAD_PATH) && $handle = opendir(UPLOAD_PATH)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != '.' && $entry != '..' && $entry != 'readme.txt' && $entry != '.gitignore') {
                $pos = strrpos($entry, '.');
                $filename = substr($entry,0,$pos);
                $filetype = substr($entry, $pos+1);
                array_push($fileList, array(
                    'name' => $filename,
                    'type' => $filetype,
                    'link' => EXPORT_PATH.$filename.'.'.$filetype,
                    'fail_link' => FAIL_PATH.$filename.'(fail)'.'.'.$filetype,
                ));
            }
        }
        closedir($handle);
    }
    $params = array('fileList' => $fileList, 'status' => '');

    return $this->renderer->render($response, 'uploads.phtml', $params);
});

$app->post('/uploads', function ($request, $response) {
    ini_set('max_execution_time', 300);
    $allPostPutVars = $request->getParsedBody();
    $files = $request->getUploadedFiles();
    if (!file_exists(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0777, true);
    }
    if (!file_exists(EXPORT_PATH)) {
        mkdir(EXPORT_PATH, 0777, true);
    }
    if (!file_exists(FAIL_PATH)) {
        mkdir(FAIL_PATH, 0777, true);
    }
    if (empty($files['uploads'])) {
        $status = 'Error : File not found!!';
    } else {
        $newfile = $files['uploads'];
        if ($newfile->getError() === UPLOAD_ERR_OK) {
            $uploadFileName = $newfile->getClientFilename();
            $uploadFileType = substr($uploadFileName, strrpos($uploadFileName, '.'));
            $uploadFileName = substr($uploadFileName, 0, strrpos($uploadFileName, '.'));
            $uploadFileName = str_replace(' ', '-', $uploadFileName);
            $uploadFileName = preg_replace('/-+/', '-', $uploadFileName);
            $uploadFileName = str_replace('+', '-', $uploadFileName);
            $uploadFileName = str_replace('.', '-', $uploadFileName);
            if ($uploadFileType == '.csv' || $uploadFileType == '.CSV') {
                $date = new DateTime();
                $formatData = new FileService();
                $filename = $date->getTimestamp();
                $uploadFile = UPLOAD_PATH.$filename.'-'.$uploadFileName.$uploadFileType;
                $exportFile = EXPORT_PATH.$filename.'-'.$uploadFileName.$uploadFileType;
                $failFile = FAIL_PATH.$filename.'-'.$uploadFileName.'(fail)'.$uploadFileType;
                $newfile->moveTo($uploadFile);
                $param = $formatData->setData($uploadFile);
                if ($param == false) {
                    $status = "Upload Fail.";
                }
                $formatData->exportData($exportFile);
                $formatData->exportFailData($failFile);
                $status = 'Upload Success.';
            } else {
                $status = 'Support .CSV File only!!';
            }
        } else {
            $status = 'Upload Fail.';
        }
    }
    $fileList = array();
    if ($handle = opendir(UPLOAD_PATH)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != '.' && $entry != '..' && $entry != 'readme.txt' && $entry != '.gitignore') {
                $pos = strrpos($entry, '.');
                $filename = substr($entry,0,$pos);
                $filetype = substr($entry, $pos+1);
                array_push($fileList, array(
                    'name' => $filename,
                    'type' => $filetype,
                    'link' => EXPORT_PATH.$filename.'.'.$filetype,
                    'fail_link' => FAIL_PATH.$filename.'(fail)'.'.'.$filetype,
                ));
            }
        }
        closedir($handle);
    }
    $params = array('fileList' => $fileList, 'status' => $status);

    return $this->renderer->render($response, 'uploads.phtml', $params);
});

$app->get('/uploads/getRaw/{filetype}/{filename}', function ($request, $response) {
    $filename = $request->getAttribute('filename');
    $filetype = $request->getAttribute('filetype');
    $data = array();
    $formatData = new FileService();
    $fileName = UPLOAD_PATH.$filename.'.'.$filetype;
    $param = $formatData->setData($fileName);
    if ($param == false) {
        return "File not found.";
    }
    $error = $param['error'];
    $params = array(
        'header' => $param['header'],
        'data' => $formatData->getRawData($data),
        'error' => $error,
    );

    return $this->renderer->render($response, 'rawdata.phtml', $params);
});

$app->get('/uploads/getData/{filetype}/{filename}', function ($request, $response) {
    $filename = $request->getAttribute('filename');
    $filetype = $request->getAttribute('filetype');
    $formatData = new FileService();
    $uploadFile = UPLOAD_PATH.$filename.'.'.$filetype;
    $exportFile = EXPORT_PATH.$filename.'.'.$filetype;
    $failFile = FAIL_PATH.$filename.'(fail)'.'.'.$filetype;
    $param = $formatData->setData($uploadFile);
    if ($param == false) {
        return "File not found.";
    }
    $error = $param['error'];
    $headerList = $formatData->getHeaderPattern();
    $exportData = $formatData->getData();
    $failData = $formatData->getFailData();
    $formatData->exportData($exportFile);
    $formatData->exportFailData($failFile);
    $params = array(
        'header' => $headerList,
        'data' => $exportData,
        'fail' => $failData,
        'link' => '../../../'.$exportFile,
        'fail_link' => '../../../'.$failFile,
        'error' => $error,
    );
    return $this->renderer->render($response, 'data.phtml', $params);
});
