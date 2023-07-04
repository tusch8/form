<?php
session_start();

// エスケープする関数
if (!function_exists("h")) {
	function h($s) {
		return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
	}
}

// ページ判定
$mode = "input";

if (isset($_POST["back"] ) && $_POST["back"]) {

	// 修正するボタン押したとき
} elseif (isset($_POST["confirm"] ) && $_POST["confirm"]) {

	// 確認ページ
	$mode = "confirm";

	// ポストされた情報
	isset($_POST['name']) ? $_SESSION["name"] = h($_POST['name']) : $_SESSION["name"] = "";
	isset($_POST['kana']) ? $_SESSION["kana"] = h($_POST['kana']) : $_SESSION["kana"] = "";
	isset($_POST['email']) ? $_SESSION["email"] = mb_convert_kana(h($_POST['email']), "rna") : $_SESSION["email"] = "";
	isset($_POST['email_conf']) ? $_SESSION["email_conf"] = mb_convert_kana(h($_POST['email_conf']), "rna") : $_SESSION["email_conf"] = "";
	if (isset($_POST['tel'])) {
		$post_tel = h($_POST['tel']);
		$post_tel = mb_ereg_replace("ー", "-", $post_tel);
		$post_tel = mb_ereg_replace("－", "-", $post_tel);
		$_SESSION["tel"] = mb_convert_kana($post_tel, "rna");
	} else {
		$_SESSION["tel"] = "";
	}
	isset($_POST['request']) ? $_SESSION["request"] = array_map('h', $_POST['request']) : $_SESSION["request"] = [];
	isset($_POST['type']) ? $_SESSION["type"] = h($_POST['type']) : $_SESSION["type"] = "";
	isset($_POST['message']) ? $_SESSION["message"] = h($_POST['message']) : $_SESSION["message"] = "";
	isset($_POST['agree']) ? $_SESSION["agree"] = h($_POST['agree']) : $_SESSION["agree"] = "";

	// エラーチェック
	$error_msg = [];

	if (!$_SESSION["name"]) {
		$error_msg[] = "名前を入力してください。";
	} elseif (mb_strlen($_SESSION["name"]) > 30) {
		$error_msg[] = "名前は30文字以内で入力してください。";
	}
	if (!$_SESSION["kana"]) {
		$error_msg[] = "ふりがなを入力してください。";
	} elseif (mb_strlen($_SESSION["kana"]) > 30) {
		$error_msg[] = "ふりがなは30文字以内で入力してください。";
	}
	if (!$_SESSION["email"]) {
		$error_msg[] = "メールアドレスを入力してください。";
	} elseif (!filter_var($_SESSION["email"], FILTER_VALIDATE_EMAIL)) {
		$error_msg[] = "正しいメールアドレスを入力してください。";
	}
	if (!$_SESSION["email_conf"]) {
		$error_msg[] = "メールアドレス確認用を入力してください。";
	} elseif ($_SESSION["email"] !== $_SESSION["email_conf"]) {
		$error_msg[] = "メールアドレスが一致しません。";
	}
	if (!preg_match('/^(0{1}\d{1,4}-{0,1}\d{1,4}-{0,1}\d{4})$/', $_SESSION["tel"])) {
		$error_msg[] = "電話番号の形式と一致しません。";
	}
	if (!$_SESSION["type"]) {
		$error_msg[] = "お問い合わせ項目を選択してください。";
	}
	if (!$_SESSION["message"]) {
		$error_msg[] = "お問い合わせ内容を入力してください。";
	} elseif (mb_strlen($_SESSION["message"]) > 1000) {
		$error_msg[] = "お問い合わせ内容は1000文字以内で入力してください。";
	}
	if (!$_SESSION["agree"]) {
		$error_msg[] = "プライバシーポリシーに同意していただけない場合、送信することが出来ません。";
	}
	if( $error_msg ){
		$mode = "input";
	}

} elseif (isset($_POST["send"] ) && $_POST["send"]) {

	// 完了ページ
	$mode = "send";

	if (isset($_SESSION["name"])) {

		date_default_timezone_set("Asia/Tokyo");
		mb_language("ja");
		mb_internal_encoding("UTF-8");

		// ヘッダー情報
		$from = mb_encode_mimeheader("株式会社ほげほげ") . " <test@test.com>"; // 送信元
		$from_mail = "test@test.com"; // 送信元メールアドレス
		$from_name = mb_encode_mimeheader("株式会社ほげほげ"); // 送信者名

		$header = null;
		$header .= "Content-Type: text/plain \r\n";
		$header .= "Return-Path: " . $from_mail . " \r\n";
		$header .= "From: " . $from ." \r\n";
		$header .= "Sender: " . $from ." \r\n";
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

		$response[] = mb_send_mail( $_SESSION["email"], $auto_reply_subject, $auto_reply_text, $header);

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

		$response[] = (mb_send_mail( "test@test.com", $admin_reply_subject, $admin_reply_text, $header));

		$_SESSION = [];

	} else {
		$mode = "input";
	}

} else {

	$_SESSION = [];

}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style.css">
	<title>フォーム</title>
</head>
<body>

	<?php if($mode == "confirm"): // 確認ページ ?>

		<div class="form">

			<p>以下の内容でお間違いがなければ送信ボタンを押してください。</p>

			<form action="" method="post">
				<table>
					<tr>
						<th>名前 ※必須</th>
						<td><?php echo $_SESSION["name"]; ?></td>
					</tr>
					<tr>
						<th>ふりがな ※必須</th>
						<td><?php echo $_SESSION["kana"]; ?></td>
					</tr>
					<tr>
						<th>メールアドレス ※必須</th>
						<td><?php echo $_SESSION["email"]; ?></td>
					</tr>
					<tr>
						<th>メールアドレス確認用 ※必須</th>
						<td><?php echo $_SESSION["email_conf"]; ?></td>
					</tr>
					<tr>
						<th>電話番号</th>
						<td><?php echo $_SESSION["tel"]; ?></td>
					</tr>
					<tr>
						<th>ご要望 複数可</th>
						<td><?php echo implode("、", $_SESSION["request"]); ?></td>
					</tr>
					<tr>
						<th>お問い合わせ項目 ※必須</th>
						<td><?php echo $_SESSION["type"]; ?></td>
					</tr>
					<tr>
						<th>お問い合わせ内容 ※必須</th>
						<td><?php echo nl2br($_SESSION["message"]); ?></td>
					</tr>
				</table>
				<button type="submit" name="back" value="修正する">修正する</button>
				<button type="submit" name="send" value="送信する">送信する</button>
			</form>
		</div>

	<?php elseif($mode == "send"): // 完了ページ ?>

		<div class="form">
			<?php if($response[0] && $response[1]): ?>
				<p>送信が完了しました。</p>
			<?php else: ?>
				<p>送信ができませんでした。</p>
			<?php endif; ?>
			<p><a href="/">トップへ戻る</a></p>
		</div>

	<?php else: // 入力ページ ?>

		<div class="form">

			<?php if( isset($error_msg) ): ?>
				<p style="color:red;">
					<?php echo implode('<br>', $error_msg ); ?>
				</p>
			<?php endif; ?>

			<form action="" method="post">
				<table>
					<tr>
						<th>名前 ※必須</th>
						<td><input type="text" name="name" value="<?php if (isset($_SESSION["name"])) { echo $_SESSION["name"]; }; ?>"></td>
					</tr>
					<tr>
						<th>ふりがな ※必須</th>
						<td><input type="text" name="kana" value="<?php if (isset($_SESSION["kana"])) { echo $_SESSION["kana"]; }; ?>"></td>
					</tr>
					<tr>
						<th>メールアドレス ※必須</th>
						<td><input type="text" name="email" value="<?php if (isset($_SESSION["email"])) { echo $_SESSION["email"]; }; ?>"></td>
					</tr>
					<tr>
						<th>メールアドレス確認用 ※必須</th>
						<td><input type="text" name="email_conf" value="<?php if (isset($_SESSION["email_conf"])) { echo $_SESSION["email_conf"]; }; ?>"></td>
					</tr>
					<tr>
						<th>電話番号</th>
						<td><input type="text" name="tel" value="<?php if(isset($_SESSION["tel"])) { echo $_SESSION["tel"]; }; ?>"></td>
					</tr>
					<tr>
						<th>ご要望 複数可</th>
						<td>
							<?php
								$request_list = ['いをしたい', 'ろをしたい', 'はをしたい', 'にをしたい'];
								foreach ($request_list as $value):
							?>
								<label><input type="checkbox" name="request[]" value="<?php echo $value; ?>"<?php if (isset($_SESSION["request"]) && in_array($value, $_SESSION["request"])) { echo ' checked'; }; ?>><?php echo $value; ?></label>
							<?php endforeach; ?>
						</td>
					</tr>
					<tr>
						<th>お問い合わせ項目 ※必須</th>
						<td>
							<?php
								$type_list = ['Aについて', 'Bについて', 'Cについて', 'その他'];
								foreach ($type_list as $value):
							?>
								<label><input type="radio" name="type" value="<?php echo $value; ?>"<?php if (isset($_SESSION["type"]) && ($_SESSION["type"] === $value)) { echo ' checked'; }; ?>><?php echo $value; ?></label>
							<?php endforeach; ?>
						</td>
					</tr>
					<tr>
						<th>お問い合わせ内容 ※必須</th>
						<td><textarea name="message"><?php if(isset($_SESSION["message"])) { echo $_SESSION["message"]; }; ?></textarea></td>
					</tr>
				</table>
				<div>プライバシーポリシーに<br><label><input type="checkbox" name="agree" value="同意する"<?php if (isset($_SESSION["agree"]) && ($_SESSION["agree"] === "同意する")) { echo ' checked'; }; ?>>同意する</label>
				</div>
				<button type="submit" name="confirm" value="確認する">確認する</button>
			</form>
		</div>

	<?php endif; ?>

</body>
</html>
