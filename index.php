<?php
session_start();

// エスケープする関数
require('./require/htmlspecialchars.php');

// ページ判定
$mode = 'input';

if (isset($_POST['back']) && $_POST['back']) {

	// 修正するボタン押したとき

} elseif (isset($_POST['confirm']) && $_POST['confirm']) {

	// 確認ページのとき
	require('./require/confirm.php');
} elseif (isset($_POST['send']) && $_POST['send']) {

	// 完了ページのとき
	require('./require/send.php');
} else {

	// 入力ページのとき
	$_SESSION = [];
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style.css">
	<title>フォーム</title>
</head>

<body>

	<?php if ($mode == 'confirm') : // 確認ページ
	?>

		<div class="form">

			<p>以下の内容でお間違いがなければ送信ボタンを押してください。</p>

			<form action="" method="post">
				<table>
					<tr>
						<th>名前 ※必須</th>
						<td><?php echo h($_SESSION['name']); ?></td>
					</tr>
					<tr>
						<th>ふりがな ※必須</th>
						<td><?php echo h($_SESSION['kana']); ?></td>
					</tr>
					<tr>
						<th>メールアドレス ※必須</th>
						<td><?php echo h($_SESSION['email']); ?></td>
					</tr>
					<tr>
						<th>メールアドレス確認用 ※必須</th>
						<td><?php echo h($_SESSION['email_conf']); ?></td>
					</tr>
					<tr>
						<th>電話番号</th>
						<td><?php echo h($_SESSION['tel']); ?></td>
					</tr>
					<tr>
						<th>ご要望 複数可</th>
						<td><?php echo h(implode("、", $_SESSION['request'])); ?></td>
					</tr>
					<tr>
						<th>お問い合わせ項目 ※必須</th>
						<td><?php echo h($_SESSION['type']); ?></td>
					</tr>
					<tr>
						<th>お問い合わせ内容 ※必須</th>
						<td><?php echo nl2br(h($_SESSION['message'])); ?></td>
					</tr>
				</table>
				<button type="submit" name="back" value="修正する">修正する</button>
				<button type="submit" name="send" value="送信する">送信する</button>
			</form>
		</div>

	<?php elseif ($mode == 'send') : // 完了ページ
	?>

		<div class="form">
			<?php if ($response[0] && $response[1]) : ?>
				<p>送信が完了しました。</p>
			<?php else : ?>
				<p>送信ができませんでした。</p>
			<?php endif; ?>
			<p><a href="/">トップへ戻る</a></p>
		</div>

	<?php else : // 入力ページ
	?>

		<div class="form">

			<?php if (isset($error_msg)) : ?>
				<p style="color:red;">
					<?php echo implode('<br>', $error_msg); ?>
				</p>
			<?php endif; ?>

			<form action="" method="post">
				<table>
					<tr>
						<th>名前 ※必須</th>
						<td><input type="text" name="name" value="<?php if (isset($_SESSION['name'])) {
																												echo h($_SESSION['name']);
																											}; ?>"></td>
					</tr>
					<tr>
						<th>ふりがな ※必須</th>
						<td><input type="text" name="kana" value="<?php if (isset($_SESSION['kana'])) {
																												echo h($_SESSION['kana']);
																											}; ?>"></td>
					</tr>
					<tr>
						<th>メールアドレス ※必須</th>
						<td><input type="text" name="email" value="<?php if (isset($_SESSION['email'])) {
																													echo h($_SESSION['email']);
																												}; ?>"></td>
					</tr>
					<tr>
						<th>メールアドレス確認用 ※必須</th>
						<td><input type="text" name="email_conf" value="<?php if (isset($_SESSION['email_conf'])) {
																															echo h($_SESSION['email_conf']);
																														}; ?>"></td>
					</tr>
					<tr>
						<th>電話番号</th>
						<td><input type="text" name="tel" value="<?php if (isset($_SESSION['tel'])) {
																												echo h($_SESSION['tel']);
																											}; ?>"></td>
					</tr>
					<tr>
						<th>ご要望 複数可</th>
						<td>
							<?php
							$request_list = ['いをしたい', 'ろをしたい', 'はをしたい', 'にをしたい'];
							foreach ($request_list as $value) :
							?>
								<label><input type="checkbox" name="request[]" value="<?php echo $value; ?>" <?php if (isset($_SESSION['request']) && in_array($value, $_SESSION['request'])) {
																																																echo ' checked';
																																															}; ?>><?php echo $value; ?></label>
							<?php endforeach; ?>
						</td>
					</tr>
					<tr>
						<th>お問い合わせ項目 ※必須</th>
						<td>
							<?php
							$type_list = ['Aについて', 'Bについて', 'Cについて', 'その他'];
							foreach ($type_list as $value) :
							?>
								<label><input type="radio" name="type" value="<?php echo $value; ?>" <?php if (isset($_SESSION['type']) && ($_SESSION['type'] === $value)) {
																																												echo ' checked';
																																											}; ?>><?php echo $value; ?></label>
							<?php endforeach; ?>
						</td>
					</tr>
					<tr>
						<th>お問い合わせ内容 ※必須</th>
						<td><textarea name="message"><?php if (isset($_SESSION['message'])) {
																						echo h($_SESSION['message']);
																					}; ?></textarea></td>
					</tr>
				</table>
				<div>プライバシーポリシーに<br><label><input type="checkbox" name="agree" value="同意する" <?php if (isset($_SESSION['agree']) && ($_SESSION['agree'] === "同意する")) {
																																											echo ' checked';
																																										}; ?>>同意する</label>
				</div>
				<button type="submit" name="confirm" value="確認する">確認する</button>
			</form>
		</div>

	<?php endif; ?>

</body>

</html>
