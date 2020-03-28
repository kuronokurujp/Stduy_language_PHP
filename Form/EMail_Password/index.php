<?php

require('emailform.php');
require('password_form.php');

/**
 * フォーム入力 
 * メールアドレス入力とパスワード入力をする
 */

// ↓の2行は定型文としよう
// E_STRICTレベル以外のエラーを報告する
error_reporting(E_ALL);
// 画面にエラーを表示させるか
ini_set("display_errors", "On");

// post送信されていた場合
if (!empty($_POST)) {
    $err_flg = false;
    $err_msg = array();

    // 入力メールテキストが正しいか
    try {
        $emailForm = new EmailForm($_POST['email']);
    } catch (EmailFormExceptin $e) {
        $err_msg['email'] = $e->getErrorText();
    }

    // 入力パスワードが正しいか
    try {
        $passwordForm = new PasswordForm($_POST['pass'], $_POST['pass_retype']);
    } catch (PasswordFormException $e) {
        if ($e->isRewordTextError()) {
            $err_msg['pass_retype'] = $e->getErrorText();
        } else {
            $err_msg['pass'] = $e->getErrorText();
        }
    }

    if (empty($err_msg)) {
        // DBにデータ送信

        // DB接続
        $dsn = 'mysql:dbname=php_form_sample;host=localhost;charset=utf8';
        $user = 'root';
        $password = 'root';

        // このオプション設定は定型文
        $option = array(
            // SQL実行失敗時の例外スロー
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // デフォルトフェッチモードを連想配列形式に設定
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // SELECTで得た結果にしてもrowCountメソッドを使えるようにする
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        );

        // PDOオブジェクト生成(DBへ接続)
        $dbn = new PDO($dsn, $user, $password, $option);

        // SQL文(クエリー作成)
        $stmt = $dbn->prepare('INSERT INTO users (email, pass, login_time) VALUES (:email, :pass, :login_time)');

        // Insert命令の穴抜けの入力枠に値を設定してSQL文を実行
        $stmt->execute(array(':email' => $emailForm->getEmail(), ':pass' => $passwordForm->getPasswordText(), ':login_time' => date('Y/m/d H:i:s')));

        header("Location:mypage.php");
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>ホームページのタイトル</title>
    <style>
        body {
            margin: 0 auto;
            padding: 150px;
            width: 25%;
            background: #fbfbfa;
        }

        h1 {
            color: #545454;
            font-size;
            20px;
        }

        form {
            overflow: hidden;
        }

        input[type="text"] {
            color: #545454;
            height: 60px;
            width: 100%;
            padding: 5px 10px;
            font-size: 16px;
            display: block;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="password"] {
            color: #545454;
            height: 60px;
            width: 100%;
            padding: 5px 10px;
            font-size: 16px;
            display: block;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            border: none;
            padding: 15px 30px;
            margin-bottom: 15px;
            background: #3d3938;
            color: white;
            float: right;
        }

        input[type="submit"]:hover {
            background: #111;
            cursor: pointer;
        }

        a {
            color: #545454;
            display: block;
        }

        a:hover {
            text-decoration: none;
        }

        .err_msg {
            color: #ff4a4b;
        }
    </style>
</head>

<body>
    <h1>ユーザー登録</h1>
    <!-- 自分自身へpost送信する action=""となっていると自分ということになるaction="mypage.php"とするとmypage.phpにpost送信-->
    <form action="" method="post">
        <span class="err_msg"><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
        <input type="text" name="email" placeholder="email" value="<?php if (!empty($_POST['email'])) echo $_POST['email']; ?>">

        <span class="err_msg"><?php if (!empty($err_msg['pass'])) echo $err_msg['pass']; ?></span>
        <input type="password" name="pass" placeholder="パスワード" value="<?php if (!empty($_POST['pass'])) echo $_POST['pass']; ?>">

        <span class="err_msg"><?php if (!empty($err_msg['pass_retype'])) echo $err_msg['pass_retype']; ?></span>
        <input type="password" name="pass_retype" placeholder="パスワード再入力" value="<?php if (!empty($_POST['pass_retype'])) echo $_POST['pass_retype']; ?>">
        <input type="submit" value="送信">
    </form>
    <a href="mypage.php">マイページ</a>
</body>

</html>