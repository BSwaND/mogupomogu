<?php
namespace uForm\classes;
use uForm\UFormConfig;

require dirname(__DIR__).'/UFormConfig.php';
require 'customPHPMailer.php';

class uForm
{
    public static $fileLog;
    public static $fileErrorLog;
    public static $log;
    public static $errorLog;

    private $config;
    private $data;
    private $files;
    private $mail;
    private $uFormUrl;

    public function __construct($customConfig = null)
    {
        $this->config = new UFormConfig;

        $this->SMTPAuth = true;
        $this->SMTPSecure = 'ssl';
        $this->Port = 465;

        self::$fileLog = dirname(__DIR__) . DIRECTORY_SEPARATOR. 'statistic.log';
        self::$fileErrorLog = dirname(__DIR__) . DIRECTORY_SEPARATOR. 'error.log';
        $this->mail = $this->PHPMailerInit($customConfig);
        $this->uFormUrl = isset($_POST['uFormUrl'])? $_POST['uFormUrl'] : null;

        $this->data = [];
        $this->files = [];
        self::$log = [];
        self::$errorLog = [];
    }

    public static function isPhone($item){
        $item = str_replace(['+', '(', ')', '-', ' '], '', $item);

        if(preg_match('/^\d{7,25}$/', $item)){
            return $item;
        }
        return null;
    }

    public static function isMail($item){
        return filter_var($item, FILTER_VALIDATE_EMAIL);
    }

    public static function lenStr($item, $option){

        $min = $option[0];
        $max = $option[1];
        $cut = isset($option[2]) ? $option[2] : true;

        $len = mb_strlen($item);
        if($len < $min){
            return null;
        }
        elseif($len > $max){
            if($cut){
                return substr($item, 0, $max);
            }
            return null;
        }

        return $item;
    }

    public static function isDate($item, $option = ['ANY'])
    {
        $action = $option[0];
        $timestamp = strtotime($item);
        if($timestamp === false){
            return null;
        }

        switch($action){
            case 'TIMESTAMP':
                return $timestamp;
                break;
            case 'SQL':
                return date('Y-m-d H:i:s', $timestamp);
            case 'ANY':
            default:
                return $item;
        }
    }

    public static function countFiles($files, $option)
    {
        $n = count($files);
        if($n > $option[0]){
            $errorFile = 'Превышен допустимый лимит файлов в одной загрузке. Загруженно: ' . $n;
            self::saveLog($errorFile);
            self::saveErrorLog($errorFile);

            return [];
        }
        return $files;
    }

    public static function maxSizeOneFile($files, $option)
    {
        $maxSize = $option[0] * 1024;
        for($i = 0; $i < count($files); $i++){
            if($files[$i]['size'] > $maxSize){
                $errorFile = 'Превышен размер файла: ' . $files[$i]['name'] . ' (' . $files[$i]['size'] . ' байта)';
                self::saveLog($errorFile);
                self::saveErrorLog($errorFile);
                unset($files[$i]);
            }
        }
        return $files;
    }

    public static function maxSizeAllFile($files, $option)
    {
        $size = 0;
        foreach($files as $file){
            $size += (int)$file['size'];
        }

        $maxSize = $option[0] * 1024;
        if($size > $maxSize){
            $errorFile = 'Превышен общий размер загружаемых файлов: (' . $size . ' байта)';
            self::saveLog($errorFile);
            self::saveErrorLog($errorFile);
            return [];
        }
        return $files;
    }

    public static function fileFormats($files, $option)
    {
        $errors = 0;
        foreach($files as $file){
            if(!in_array($file['type'], $option)){
                $errorFile = 'Недопустимый тип "'. $file['type'] .'" файла: (' . $file['name'] . ')';
                self::saveLog($errorFile);
                self::saveErrorLog($errorFile);

                $errors++;
            }
        }

        return $errors ? [] : $files;
    }

    public static function saveLog($str)
    {
        self::$log[] = $str;
        $str = date('Y-m-d H:i:s') . ': ' . $str . PHP_EOL;
        @file_put_contents(self::$fileLog, $str, FILE_APPEND|LOCK_EX);

        return $str;
    }

    public static function saveErrorLog($str, $saveFile = true, $flag = 'last')
    {
        if(empty($str)){
            return '';
        }

        $str = date('Y-m-d H:i:s') . ': ' . $str . PHP_EOL;
        self::$errorLog[] = $str;

        switch($flag){
            case 'all':
                $str = implode(PHP_EOL, self::$errorLog);
                break;
            case 'last':
                break;
        }
        @file_put_contents(self::$fileErrorLog, $str, FILE_APPEND|LOCK_EX);

        return $str;
    }

    public static function saveDumpDataForm()
    {
        ob_start();
        var_dump($_POST);
        var_dump($_FILES);
        $str = ob_get_clean();
        @file_put_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR .'dump.log', $str);

        return $str;
    }

    public function getFormData()
    {
        return $this->data;
    }

    public function PHPMailerInit($customConfig)
    {
        /**
         * @var bool $isHtml
         * @var string $fromMail
         * @var string $receiverMails
         * @var string $bccMails
         * @var bool $isSmtp
         * @var array $smtp_config
         */

        if($customConfig != null){
            if($customConfig == 'tech'){
                if(!isset($this->config->techMails)){
                    return false;
                }
                $this->config->receiverMails = $this->config->techMails;
                $this->config->bccMails = '';
            }
            else{
                if(isset($customConfig['fromMail'])) $this->config->fromMail = $customConfig['fromMail'];
                if(isset($customConfig['receiverMails'])) $this->config->receiverMails = $customConfig['receiverMails'];
                if(isset($customConfig['bccMails'])) $this->config->bccMails = $customConfig['bccMails'];
                if(isset($customConfig['isHtml'])) $this->config->isHtml = $customConfig['isHtml'];
                if(isset($customConfig['SMTPAuth'])) $this->config->SMTPAuth = $customConfig['SMTPAuth'];
                if(isset($customConfig['SMTPSecure'])) $this->config->SMTPSecure = $customConfig['SMTPSecure'];
                if(isset($customConfig['port'])) $this->config->Port = $customConfig['port'];
            }
        }


        $mail = new customPHPMailer\PHPMailer();

        //################## Настройки SMTP ##################
        if($this->config->isSmtp){
            $mail->isSMTP();
            $mail->SMTPAuth = $this->config->smtp['SMTPAuth'];
            $mail->SMTPSecure = $this->config->smtp['SMTPSecure'];
            $mail->Host = $this->config->smtp['host'];
            $mail->Port = $this->config->smtp['port'];
            $mail->Username = $this->config->smtp['username'];
            $mail->Password = $this->config->smtp['password'];
        }

        //################## отправитель и получатели ##################
        $mail->setFrom($this->config->fromMail); // Email, с которого отправляется письма (крайне желательно, чтоб совпадал с email-ом SMTP (если используется)

        // Email получателей. Все emails видны в получателях
        $receiverMailsArr = explode(',', $this->config->receiverMails);
        foreach ($receiverMailsArr as $mailAddr) {
            $mail->addAddress(trim($mailAddr));
        }

        // Email скрытых получателей
        if(isset($this->config->bccMails)){
            $bccMailsArr = explode(',', $this->config->bccMails);

            foreach ($bccMailsArr as $mailAddr) {
                $mail->addBCC(trim($mailAddr));
            }
        }

        $mail->isHTML($this->config->isHtml); // формат письма HTML

        return $mail;
    }

    public function getPostData($inputs)
    {
        foreach ($inputs as $name => $valid){

            if($_POST['nospam'] != 'uform-empty'){
                $str = $this->saveLog('This is bot! ');
                self::saveErrorLog($str);
                $this->sendTechInfo($str);
                return 'ISBOT';
            }

            if(!isset($_POST[$name])){
                $str = $this->saveLog('Input "'.$name.'" is not exists!');
                self::saveErrorLog($str);
                $this->data[$name] = null;
                continue;
            }

            if(is_array($_POST[$name])){
                $val = [];
                foreach($_POST[$name] as $one){
                    $val[] = htmlspecialchars(trim($one));
                }
            }
            else{
                $val = htmlspecialchars(trim($_POST[$name]));
            }

            if(empty($val)){
                $this->data[$name] = null;
                continue;
            }

            if(is_array($valid)){
                foreach($valid as $validName => $params){
                    if(is_numeric($validName)){
                        $val = self::$params($val);
                    }
                    else{
                        $val = self::$validName($val, $params);
                    }
                }
                $this->data[$name] = $val;
            }
            else {
                $this->data[$name] = empty($valid)? $val : self::$valid($val);
            }
        }

        if(empty($this->data)){
            $postTest = empty($_POST)? 'and POST is empty!' : 'bat POST not empty!';

            $str = $this->saveLog('Empty data in getPostData()! ' . $postTest);
            self::saveErrorLog($str);
            $this->sendTechInfo($str);
        }

        return $this->data;
    }

    public function getLoadFiles($files)
    {
        if(empty($files)){
            return null;
        }
        foreach($files as $inputName => $valid){
            $loadFiles = [];
            if(is_array($_FILES[$inputName]['tmp_name'])) {
                for ($ct = 0; $ct < count($_FILES[$inputName]['tmp_name']); $ct++) {
                    if($_FILES[$inputName]['size'][$ct] > 0) {
                        $loadFiles[] = [
                            'name' => $_FILES[$inputName]['name'][$ct],
                            'type' => $_FILES[$inputName]['type'][$ct],
                            'tmp_name' => $_FILES[$inputName]['tmp_name'][$ct],
                            'error' => $_FILES[$inputName]['error'][$ct],
                            'size' => $_FILES[$inputName]['size'][$ct],
                        ];
                    }
                }
            }
            else{
                if($_FILES[$inputName]['size'] > 0) {
                    $loadFiles[] = [
                        'name' => $_FILES[$inputName]['name'],
                        'type' => $_FILES[$inputName]['type'],
                        'tmp_name' => $_FILES[$inputName]['tmp_name'],
                        'error' => $_FILES[$inputName]['error'],
                        'size' => $_FILES[$inputName]['size'],
                    ];
                }
            }

            if(empty($loadFiles)){
                $this->files[$inputName] = [];
                continue;
            }

            if(is_array($valid)){
                foreach($valid as $validName => $params){
                    if(is_numeric($validName)){
                        $loadFiles = self::$params($loadFiles);
                    }
                    else{
                        $loadFiles = self::$validName($loadFiles, $params);
                    }
                }
                $this->files[$inputName] = $loadFiles;
            }
            else {
                $this->files[$inputName] = self::$valid($loadFiles);
            }
        }

        return $this->files;
    }

    public function testRequires($requires)
    {
        if(empty($this->data)){
            $str = $this->saveLog('Empty data!');
            self::saveErrorLog($str);
            $this->sendTechInfo($str);
            return $str;
        }

        $fieldsFail = [];
        foreach ($requires as $require){
            if(!isset($this->data[$require]) || empty($this->data[$require])){
                $fieldsFail[] = $require;
            }
        }

        if(!empty($fieldsFail)){
            $errorRequired = 'Empty required fields: ' . implode(', ', $fieldsFail);
            $this->saveLog($errorRequired);
            self::saveErrorLog($errorRequired);
            $this->sendTechInfo($errorRequired);

            return $fieldsFail;
        }
        return true;
    }

    public function sendTechInfo($msg){
        ob_start();
        echo '<br><pre>';
        echo '<b>$_POST:</b><br>';
        htmlspecialchars(var_dump($_POST));
        echo '<br><b>$_FILES:</b><br>';
        htmlspecialchars(var_dump($_FILES));
        echo '</pre>';
        $dumpPost = ob_get_clean();

        $techMail = new self();
        $subj = 'Error sending a message: ' . $_SERVER['SERVER_NAME'];
        $msg = 'Form url: ' . $this->uFormUrl . '  <br>  ' . $msg . $dumpPost;
        $techMail->sendMail($subj, $msg, 'tech');
    }

    public function sendMail($mail_subject, $mail_body, $customConfig = null)
    {
        if(!empty($customConfig)){
            $this->mail = $this->PHPMailerInit($customConfig);
            if(empty($this->mail)){
                $str = $this->saveLog('Error init PHPMailer: ' . __FILE__ . ':' . __LINE__);
                self::saveErrorLog($str);
                return false;
            }
        }

        //################## Прикрепление файлов ##################
        foreach($this->files as $files){
            foreach($files as $file){
                $uploadfile = tempnam(sys_get_temp_dir(), sha1($file['name']));
                $filename = $file['name'];

                if (move_uploaded_file($file['tmp_name'], $uploadfile)) {
                    $this->mail->addAttachment($uploadfile, $filename);
                }
                else {
                    $str = $this->saveLog('Failed to move file "'. $file['tmp_name'] .'" to ' . $uploadfile);
                    self::saveErrorLog($str);
                    $this->sendTechInfo($str);
                }
            }
        }

        //################## Заголовок письма ##################
        $this->mail->Subject = $mail_subject;

        //################## Текст письма ##################
        $this->mail->Body = $mail_body;

        //################## Результат отправки письма ##################
        if(!$this->mail->send()) {
            $resuletSend = false;
            $resultMsg = 'Message could not be sent. Mailer Error: ' . $this->mail->ErrorInfo;
            self::saveErrorLog($resultMsg);
            $this->sendTechInfo($resultMsg);
        } else {
            $resuletSend = true;
            $resultMsg = 'Message sended';
        }
        $this->saveLog($resultMsg);

        return [$resuletSend, $resultMsg];
    }
}