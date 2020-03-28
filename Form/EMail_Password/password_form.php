<?php

/**
 * パスワードフォームオブジェクトクラスの例外クラス
 */
class PasswordFormException extends Exception
{
    public static $ErrorID_None = -1;
    public static $ErrorID_Empty = 0;
    public static $ErrorID_TextFormatMiss = 1;
    public static $ErrorID_TextLengthMiss = 2;
    public static $ErrorID_RewordDiscord = 3;

    private $_errorList = array(
        '入力必須です',
        '半角英数字のみご利用いただけます',
        '6文字以上で入力してください',
        'バスワードを一致して下さい'
    );

    private $_errorID = 0;
    private $_errorText = '';
    private $_rewordTextError = false;

    public function __construct($in_rewordTextError, $in_errorID)
    {
        $this->_errorID = $in_errorID;
        $this->_errorText = $this->_errorList[$this->_errorID];
        $this->_rewordTextError = $in_rewordTextError;
    }

    public function getErrorText()
    {
        return $this->_errorText;
    }

    public function isRewordTextError()
    {
        return $this->_rewordTextError;
    }
}

/**
 * パスワードフォームオブジェクトクラス
 */
class PasswordForm
{
    private $_pass = '';
    private $_passReword = '';

    public function getPasswordText()
    {
        return $this->_pass;
    }

    public function __construct($in_passwordText, $in_rewordText)
    {
        // 空文字か
        if (empty($in_passwordText)) {
            throw new PasswordFormException(false, PasswordFormException::$ErrorID_Empty);
        }

        if (empty($in_rewordText)) {
            throw new PasswordFormException(true, PasswordFormException::$ErrorID_Empty);
        }

        $pass = htmlspecialchars($in_passwordText, ENT_QUOTES);
        $validPasswordFormatErrorID = $this->_isValidPasswordFormat($pass);
        if ($validPasswordFormatErrorID != PasswordFormException::$ErrorID_None) {
            throw new PasswordFormException(false, $validPasswordFormatErrorID);
        }

        $passReword = htmlspecialchars($in_rewordText, ENT_QUOTES);
        $validPasswordFormatErrorID = $this->_isValidPasswordFormat($passReword);
        if ($validPasswordFormatErrorID != PasswordFormException::$ErrorID_None) {
            throw new PasswordFormException(true, $validPasswordFormatErrorID);
        }

        // 再入力と一致しているか
        if ($pass !== $passReword) {
            throw new PasswordFormException(true, PasswordFormException::$ErrorID_RewordDiscord);
        }

        $this->_pass = $pass;
        $this->_passReword = $passReword;
    }

    // パスワードが適切なフォーマットになっているか
    private function _isValidPasswordFormat($in_text)
    {
        // 半角文字の大文字小文字と数字のみのテキストか
        if (!preg_match("/^[a-zA-Z0-9]+$/", $in_text)) {
            return PasswordFormException::$ErrorID_TextFormatMiss;
        } elseif (mb_strlen($in_text) < 6) {
            // 6. パスワードとパスワード再入力が6文字以上でない場合
            return PasswordFormException::$ErrorID_TextLengthMiss;
        }

        return PasswordFormException::$ErrorID_None;
    }
}
