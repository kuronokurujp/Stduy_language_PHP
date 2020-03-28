<?php

/**
 * メールフォームクラス内の例外クラス
 */
class EmailFormException extends Exception
{
    public static $ErrorID_Empty = 0;
    public static $ErrorID_TextFormatMiss = 1;

    private $_errorList = array(
        '入力必須です',
        'Emailの形式で入力してください'
    );

    private $_errorID = 0;
    private $_errorText = '';

    public function getId()
    {
        return $this->_errorID;
    }

    public function getErrorText()
    {
        return $this->_errorText;
    }

    public function __construct($in_errorID)
    {
        $this->_errorID = $in_errorID;
        $this->_errorText = $this->_errorList[$this->_errorID];
    }
}

/**
 * メールフォームクラス
 */
class EmailForm
{
    private $_email = '';

    public function getEmail()
    {
        return $this->_email;
    }

    // 初期化
    public function __construct($in_emailText)
    {
        if (empty($in_emailText)) {
            throw new EmailFormException(EmailFormException::$ErrorID_Empty);
        }

        // メールテキストにhtmlの特殊文字が入っている場合はその文字を変換する
        $in_emailText = htmlspecialchars($in_emailText, ENT_QUOTES);

        // 入力したテキストがemailの書式になっているかチェック
        if ($this->_IsValidTextFormat($in_emailText) === false) {
            throw new EmailFormException(EmailFormException::$ErrorID_TextFormatMiss);
        }

        $this->_email = $in_emailText;

        return true;
    }

    // テキストがEMailの書式に従がっているか
    private function _isValidTextFormat($text)
    {
        if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $text)) {
            return false;
        }

        return true;
    }
}
