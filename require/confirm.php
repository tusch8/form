<?php
$mode = 'confirm';

// ポストされた情報
isset($_POST['name']) ? $_SESSION['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS) : $_SESSION['name'] = '';
isset($_POST['kana']) ? $_SESSION['kana'] = filter_input(INPUT_POST, 'kana', FILTER_SANITIZE_SPECIAL_CHARS) : $_SESSION['kana'] = '';
isset($_POST['email']) ? $_SESSION['email'] = mb_convert_kana(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL), 'rna') : $_SESSION['email'] = '';
isset($_POST['email_conf']) ? $_SESSION['email_conf'] = mb_convert_kana(filter_input(INPUT_POST, 'email_conf', FILTER_VALIDATE_EMAIL), 'rna') : $_SESSION['email_conf'] = '';
if (isset($_POST['tel'])) {
	$post_tel = filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_SPECIAL_CHARS);
	$post_tel = mb_ereg_replace('ー', '-', $post_tel); // 全角ハイフンを置き換え(ハイフンには色々種類があるが他は知らん)
	$post_tel = mb_ereg_replace('－', '-', $post_tel);
	$_SESSION['tel'] = mb_convert_kana($post_tel, 'rna');
} else {
	$_SESSION['tel'] = '';
}
isset($_POST['request']) ? $_SESSION['request'] = filter_input(INPUT_POST, 'request', FILTER_REQUIRE_ARRAY): $_SESSION['request'] = [];
isset($_POST['type']) ? $_SESSION['type'] = filter_input(INPUT_POST, 'type', FILTER_DEFAULT) : $_SESSION['type'] = '';
isset($_POST['message']) ? $_SESSION['message'] = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS) : $_SESSION['message'] = '';
isset($_POST['agree']) ? $_SESSION['agree'] = filter_input(INPUT_POST, 'agree', FILTER_REQUIRE_ARRAY) : $_SESSION['agree'] = '';

// エラーチェック
$error_msg = [];

if (!$_SESSION['name']) {
	$error_msg[] = '名前を入力してください。';
} elseif (mb_strlen($_SESSION['name']) > 30) {
	$error_msg[] = '名前は30文字以内で入力してください。';
}
if (!$_SESSION['kana']) {
	$error_msg[] = 'ふりがなを入力してください。';
} elseif (mb_strlen($_SESSION['kana']) > 30) {
	$error_msg[] = 'ふりがなは30文字以内で入力してください。';
}
if (!$_SESSION['email']) {
	$error_msg[] = 'メールアドレスを入力してください。';
} elseif (!filter_var($_SESSION['email'], FILTER_VALIDATE_EMAIL)) {
	$error_msg[] = '正しいメールアドレスを入力してください。';
}
if (!$_SESSION['email_conf']) {
	$error_msg[] = 'メールアドレス確認用を入力してください。';
} elseif ($_SESSION['email'] !== $_SESSION['email_conf']) {
	$error_msg[] = 'メールアドレスが一致しません。';
}
if (!preg_match('/^(0{1}\d{1,4}-{0,1}\d{1,4}-{0,1}\d{4})$/', $_SESSION['tel'])) {
	$error_msg[] = '電話番号の形式と一致しません。';
}
if (!$_SESSION['type']) {
	$error_msg[] = 'お問い合わせ項目を選択してください。';
}
if (!$_SESSION['message']) {
	$error_msg[] = 'お問い合わせ内容を入力してください。';
} elseif (mb_strlen($_SESSION['message']) > 1000) {
	$error_msg[] = 'お問い合わせ内容は1000文字以内で入力してください。';
}
if (!$_SESSION['agree']) {
	$error_msg[] = 'プライバシーポリシーに同意していただけない場合、送信することが出来ません。';
}
if ($error_msg) {
	$mode = 'input';
}
