<?php

$mode = 'send';

if (isset($_SESSION['name'])) {

	date_default_timezone_set('Asia/Tokyo');
	mb_language('ja');
	mb_internal_encoding('UTF-8');

	// ヘッダー情報
	$from = mb_encode_mimeheader("株式会社ほげほげ") . " <test@test.com>"; // 送信元
	$from_mail = "test@test.com"; // 送信元メールアドレス
	$from_name = mb_encode_mimeheader("株式会社ほげほげ"); // 送信者名

	$header = null;
	$header .= "Content-Type: text/plain \r\n";
	$header .= "Return-Path: " . $from_mail . " \r\n";
	$header .= "From: " . $from . " \r\n";
	$header .= "Sender: " . $from . " \r\n";
	$header .= "Reply-To: " . $from_mail . " \r\n";
	$header .= "Organization: " . $from_name . " \r\n";
	$header .= "X-Sender: " . $from_mail . " \r\n";
	$header .= "X-Priority: 3 \r\n";

	// 送信者への自動返信メール
	$auto_reply_subject = null;
	$auto_reply_subject = "お問い合わせありがとうございます。";
	$auto_reply_text = null;
	$auto_reply_text = "この度は、お問い合わせ頂き誠にありがとうございます。" . "\n";
	$auto_reply_text .= "下記の内容でお問い合わせを受け付けました。" . "\n\n";
	$auto_reply_text .= "名前：" . $_SESSION["name"] . "\n";
	$auto_reply_text .= "ふりがな：" . $_SESSION["kana"] . "\n";
	$auto_reply_text .= "メールアドレス：" . $_SESSION["email"] . "\n";
	$auto_reply_text .= "電話番号：" . $_SESSION["tel"] . "\n";
	$auto_reply_text .= "ご要望：" . implode("、", $_SESSION["request"]) . "\n";
	$auto_reply_text .= "お問い合わせ項目：" . $_SESSION["type"] . "\n";
	$auto_reply_text .= "お問い合わせ内容：\n" . $_SESSION["message"] . "\n\n";
	$auto_reply_text .= "送信日時：" . date("Y-m-d H:i");

	$response[] = mb_send_mail($_SESSION['email'], $auto_reply_subject, $auto_reply_text, $header);

	// 運営側への自動返信メール
	$admin_reply_subject = null;
	$admin_reply_subject = "お問い合わせを受け付けました";
	$admin_reply_text = null;
	$admin_reply_text = "下記の内容でお問い合わせがありました。\n\n";
	$admin_reply_text .= "名前：" . $_SESSION["name"] . "\n";
	$admin_reply_text .= "ふりがな：" . $_SESSION["kana"] . "\n";
	$admin_reply_text .= "メールアドレス：" . $_SESSION["email"] . "\n";
	$admin_reply_text .= "電話番号：" . $_SESSION["tel"] . "\n";
	$admin_reply_text .= "ご要望：" . implode("、", $_SESSION["request"]) . "\n";
	$admin_reply_text .= "お問い合わせ項目：" . $_SESSION["type"] . "\n";
	$admin_reply_text .= "お問い合わせ内容：\n" . $_SESSION["message"] . "\n\n";
	$admin_reply_text .= "送信日時：" . date("Y-m-d H:i");

	$response[] = (mb_send_mail("test@test.com", $admin_reply_subject, $admin_reply_text, $header));

	$_SESSION = [];
} else {
	$mode = 'input';
}
